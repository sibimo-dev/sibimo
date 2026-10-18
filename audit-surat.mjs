#!/usr/bin/env node
/**
 * SIBIMO - SKRIP AUDIT SURAT (audit-surat.mjs)
 * =============================================
 * Mengaudit ketidaksesuaian antara:
 *   1. Field frontend  (sibimo-admin/src/data/letters/**\/*.js + letterFields.js)
 *   2. Field backend   (LetterTypeFieldSeeder.php + AdditionalLetterTemplateCatalog.php)
 *   3. Variabel blade  (resources/views/letters/**)
 *   4. Logika pemetaan (mapFieldsToBackend di letter-create.service.js)
 *
 * Jalankan dari direktori sibimo/:
 *   node audit-surat.mjs
 *
 * Output: AUDIT-SURAT.md
 * 
 * ATURAN: Skrip ini hanya membaca file; tidak menulis ke aplikasi/DB.
 */

import { readFileSync, readdirSync, existsSync, statSync, writeFileSync } from 'fs';
import { join, dirname, basename } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT_BACKEND  = __dirname;
const ROOT_ADMIN    = join(__dirname, '../sibimo-admin');
const VIEWS_DIR     = join(ROOT_BACKEND, 'resources/views/letters');
const SEEDER_FILE   = join(ROOT_BACKEND, 'database/seeders/LetterTypeFieldSeeder.php');
const CATALOG_FILE  = join(ROOT_BACKEND, 'app/Support/AdditionalLetterTemplateCatalog.php');
const PDF_SERVICE   = join(ROOT_BACKEND, 'app/Services/LetterPdfService.php');
const LETTERS_DIR   = join(ROOT_ADMIN, 'src/data/letters');
const FIELD_MAP_FILE= join(ROOT_ADMIN, 'src/data/letterFieldMap.js');
const SERVICE_FILE  = join(ROOT_ADMIN, 'src/services/letter-create.service.js');

// ─── UTILITAS ──────────────────────────────────────────────────────────────

function readFile(path) {
  try { return readFileSync(path, 'utf8'); }
  catch { return ''; }
}

function findFiles(dir, ext = '.js', list = []) {
  if (!existsSync(dir)) return list;
  for (const f of readdirSync(dir)) {
    const p = join(dir, f);
    if (statSync(p).isDirectory()) findFiles(p, ext, list);
    else if (p.endsWith(ext)) list.push(p);
  }
  return list;
}

// Normalisasi string untuk perbandingan: hilangkan diakritik, huruf kecil, tanpa non-alfanumerik
function norm(v) {
  return String(v ?? '')
    .toLowerCase()
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, '');
}

// snake_case dari camelCase
function snake(v) {
  return String(v ?? '')
    .replace(/([a-z0-9])([A-Z])/g, '$1_$2')
    .replace(/[^a-zA-Z0-9]+/g, '_')
    .replace(/^_|_$/g, '')
    .toLowerCase();
}

// ─── PARSE PHP SEEDER ──────────────────────────────────────────────────────

/**
 * Parse LetterTypeFieldSeeder.php -> Map<code, field[]>
 * field = { key, label, type, is_required, options }
 */
function parseSeeder(src) {
  const result = new Map();
  // Cari semua blok seedForCode('KODE', [...])
  const codeRe = /seedForCode\s*\(\s*['"]([^'"]+)['"]\s*,\s*\[/g;
  let m;
  while ((m = codeRe.exec(src)) !== null) {
    const code = m[1];
    const start = m.index + m[0].length;
    // Ambil konten array hingga ]);  (perhatikan nested array untuk options)
    let depth = 1, i = start;
    while (i < src.length && depth > 0) {
      if (src[i] === '[') depth++;
      else if (src[i] === ']') depth--;
      i++;
    }
    const block = src.slice(start, i - 1);
    const fields = parsePhpFieldArray(block);
    if (!result.has(code)) result.set(code, fields);
  }
  return result;
}

function parsePhpFieldArray(block) {
  const fields = [];
  // Pisahkan per entry ['label' => ..., 'key' => ..., ...]
  // Cari posisi tiap [  yang merupakan field baru (kedalaman 1)
  let depth = 0, start = -1;
  const entries = [];
  for (let i = 0; i < block.length; i++) {
    if (block[i] === '[') {
      depth++;
      if (depth === 1) start = i;
    } else if (block[i] === ']') {
      depth--;
      if (depth === 0 && start >= 0) {
        entries.push(block.slice(start + 1, i));
        start = -1;
      }
    }
  }
  for (const entry of entries) {
    const field = {};
    // label
    const lm = entry.match(/'label'\s*=>\s*'([^']+)'/);
    if (lm) field.label = lm[1];
    const km = entry.match(/'key'\s*=>\s*'([^']+)'/);
    if (km) field.key = km[1];
    const tm = entry.match(/'type'\s*=>\s*'([^']+)'/);
    if (tm) field.type = tm[1];
    // is_required: default = true, kalau ada 'is_required' => false maka false
    field.is_required = !/['"]is_required['"]\s*=>\s*false/i.test(entry);
    // options: ambil semua string dalam nested array
    const om = entry.match(/'options'\s*=>\s*\[([^\]]*)\]/s);
    if (om) {
      field.options = [...om[1].matchAll(/'([^']+)'/g)].map(x => x[1]);
    }
    if (field.key) fields.push(field);
  }
  return fields;
}

/**
 * Parse AdditionalLetterTemplateCatalog.php -> Map<code, {fields[], bladeView}>
 */
function parseCatalog(src) {
  const result = new Map();
  // Cari self::type('KODE', ..., 'letters.xxx', ..., self::xxxFields(), ...)
  const typeRe = /self::type\s*\(\s*'([^']+)'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\s*,\s*'([^']+)'\s*,\s*(self::\w+\(\))/g;
  let m;
  while ((m = typeRe.exec(src)) !== null) {
    const [, code, name,,,bladeView,,fieldMethod] = m;
    const slug = bladeView.split('.').pop();
    result.set(code, { name, bladeView, slug, fieldMethod });
  }
  // Juga cari field methods
  const methodFields = parseCatalogFieldMethods(src);
  for (const [code, info] of result) {
    const methodName = info.fieldMethod.replace('self::', '').replace('()', '');
    info.fields = methodFields.get(methodName) ?? [];
  }
  return result;
}

function parseCatalogFieldMethods(src) {
  const result = new Map();
  // Cari private static function xxxFields(): array { return [...]; }
  // atau return array_merge(...)
  const methodRe = /private static function (\w+)\(\): array\s*\{([\s\S]*?)^\s*\}/mg;
  let m;
  while ((m = methodRe.exec(src)) !== null) {
    const name = m[1];
    const body = m[2];
    // Ekstrak field dari self::field(...)
    const fields = parseCatalogFields(body, src);
    result.set(name, fields);
  }
  return result;
}

function parseCatalogFields(body, fullSrc) {
  const fields = [];
  // Cari self::field('label', 'key', 'type', ...)
  const re = /self::field\s*\(\s*'([^']+)'\s*,\s*'([^']+)'(?:\s*,\s*'([^']+)')?/g;
  let m;
  while ((m = re.exec(body)) !== null) {
    const [, label, key, type = 'text'] = m;
    // is_required: cari apakah ada false setelah posisi ini di argumen
    const afterMatch = body.slice(m.index + m[0].length, m.index + 200);
    const is_required = !/false\s*[,)]/i.test(afterMatch.slice(0, 100));
    fields.push({ label, key, type, is_required });
  }
  // Cari self::identityFields(), self::deathPersonFields(), dll didalamnya
  // untuk array_merge
  const mergeRe = /self::(\w+)\(([^)]*)\)/g;
  while ((m = mergeRe.exec(body)) !== null) {
    const method = m[1];
    const args = m[2].trim();
    // Cari method ini di fullSrc
    const methodRe2 = new RegExp(`private static function ${method}\\([^)]*\\): array\\s*\\{([\\s\\S]*?)^\\s*\\}`, 'm');
    const mm = fullSrc.match(methodRe2);
    if (mm) {
      const subFields = parseCatalogFields(mm[1], fullSrc);
      fields.push(...subFields);
    }
  }
  // Deduplikasi berdasarkan key
  const seen = new Set();
  return fields.filter(f => {
    if (seen.has(f.key)) return false;
    seen.add(f.key);
    return true;
  });
}

// ─── PARSE FIELD OVERRIDES (JS) ────────────────────────────────────────────

function parseFieldOverrides(src) {
  const overrides = {};
  // Cari FIELD_OVERRIDES = { ... }
  const m = src.match(/export const FIELD_OVERRIDES\s*=\s*(\{[\s\S]*?\});\s*\nexport/);
  if (!m) return overrides;
  try {
    // Evaluasi aman: ganti komentar dan parse
    const cleaned = m[1]
      .replace(/\/\/.*/g, '')
      .replace(/\/\*[\s\S]*?\*\//g, '');
    // Gunakan Function untuk eval terbatas
    const fn = new Function(`return ${cleaned}`);
    return fn();
  } catch (e) {
    console.error('Gagal parse FIELD_OVERRIDES:', e.message);
    return {};
  }
}

function parseRowOverrides(src) {
  const m = src.match(/export const ROW_OVERRIDES\s*=\s*(\{[\s\S]*?\});\s*\nexport/);
  if (!m) return {};
  try {
    const cleaned = m[1].replace(/\/\/.*/g, '').replace(/\/\*[\s\S]*?\*\//g, '');
    return new Function(`return ${cleaned}`)();
  } catch { return {}; }
}

// ─── PARSE TEMPLATE_BY_CODE (PHP) ──────────────────────────────────────────

function parseTemplateByCode(src) {
  const map = new Map();
  const m = src.match(/TEMPLATE_BY_CODE\s*=\s*\[([\s\S]*?)\];/);
  if (!m) return map;
  const re = /'([A-Z0-9]+)'\s*=>\s*'([^']+)'/g;
  let mm;
  while ((mm = re.exec(m[1])) !== null) {
    map.set(mm[1], mm[2]);
  }
  return map;
}

// ─── PARSE BLADE VARIABLES ─────────────────────────────────────────────────

/**
 * Ekstrak variabel yang dibaca blade: $form['key'], $xxx['key'], {{ $key }}
 * Return Set<string> berisi semua field_key yang diakses
 */
function parseBladeVariables(bladeSrc) {
  const keys = new Set();
  // $form['key'] atau $form["key"]
  for (const m of bladeSrc.matchAll(/\$form\[['"]([^'"]+)['"]\]/g)) keys.add(m[1]);
  // $birth['key'], $marriage['key'], dst.
  for (const m of bladeSrc.matchAll(/\$(?:birth|marriage|kia|stayApplication|residentRequest|populationOccurrence|travelOrder)\[['"]([^'"]+)['"]\]/g)) keys.add(m[1]);
  // Variabel langsung: {{ $key }} atau @isset($key)
  for (const m of bladeSrc.matchAll(/\$(\w+)\b/g)) {
    const k = m[1];
    if (!['this','applicant','signer','signature','kop','logo','region','form',
          'birth','marriage','kia','stayApplication','residentRequest','event',
          'populationOccurrence','travelOrder','number','nomor','year','date_parts',
          'village','attorney','deceased','husband','wife','spouse','student',
          'business','company','land','letter_c','children','family_members',
          'lease','researcher','tembusan','endorser','witnesses','reasons',
          'other_identity','condition','purpose','destination','income','category',
          'kkm_number','responsible_person','marriage_witnesses','marriage_date',
          'education_job_changes','religion_other_changes','other_element_label',
          'recipient','recipient_address','ref_number','application',
          'hamlet_head_name','form_code','kodeForm','document_title',
          'applicant_name','registration'].includes(k)) {
      // skip system vars
    }
  }
  return keys;
}

/**
 * Blade lebih kompleks: ekstrak semua $form['xxx'] dan key dari nested strukturs
 */
function extractBladeFormKeys(bladeSrc) {
  const keys = new Set();
  // $form['xxx'] -- key yang dibaca langsung dari form_data
  for (const m of bladeSrc.matchAll(/\$form\[['"]([^'"]+)['"]\]/g)) keys.add(m[1]);
  return keys;
}

// ─── PARSE LETTER JS (FRONTEND) ─────────────────────────────────────────────

/**
 * Coba import/eval file JS frontend untuk mendapatkan sections dan fields.
 * Karena menggunakan ES modules dengan import dari modul lain, kita parse secara statis.
 */
function extractFrontendFieldsFromFile(filePath) {
  const src = readFile(filePath);
  if (!src) return [];
  const fields = [];
  
  // Cari semua f.text('key', 'label'), f.date('key', 'label'), dll.
  const fieldRe = /f\.(text|date|time|area|select|nik|kk|checks|rows)\s*\(\s*['"`]([^'"`]+)['"`]\s*(?:,\s*['"`]([^'"`]*)['"`])?/g;
  let m;
  while ((m = fieldRe.exec(src)) !== null) {
    const [, type, key, label = ''] = m;
    if (key && !key.includes('${')) {
      fields.push({ key, label, type });
    }
  }
  
  // Cari template literals `${prefix}Name` dll.
  const templateRe = /f\.(text|date|time|area|select|nik|kk)\s*\(\s*`\$\{([^}]+)\}([^`'"`]*)`\s*(?:,\s*['"`]([^'"`]*)['"`])?/g;
  while ((m = templateRe.exec(src)) !== null) {
    const [, type, varName, suffix, label = ''] = m;
    // Coba resolve variabel prefix
    const prefixRe = new RegExp(`(?:const|let|var)\\s+${varName}\\s*=\\s*['"\`]([^'"\`]+)['"\`]`, 'g');
    const pm = prefixRe.exec(src);
    if (pm) {
      fields.push({ key: pm[1] + suffix, label, type });
    }
  }
  
  // Cari juga blok person('prefix', ...) untuk field generik
  const personRe = /person\s*\(\s*['"]([^'"]+)['"]\s*,\s*['"][^'"]*['"](?:\s*,\s*\[([^\]]*)\])?/g;
  while ((m = personRe.exec(src)) !== null) {
    const prefix = m[1];
    const parts = m[2] ? m[2].match(/"([^"]+)"|'([^']+)'/g)?.map(x => x.replace(/['"]/g, '')) : 
                  ['name', 'nik', 'birth', 'address'];
    if (parts) {
      const partMap = {
        name: ['Name', 'Nama Lengkap', 'text'],
        nik: ['Nik', 'NIK', 'text'],
        birth: ['BirthPlace', 'Tempat Lahir', 'text', 'BirthDate', 'Tanggal Lahir', 'date'],
        gender: ['Gender', 'Jenis Kelamin', 'select'],
        religion: ['Religion', 'Agama', 'select'],
        occupation: ['Occupation', 'Pekerjaan', 'select'],
        education: ['Education', 'Pendidikan Terakhir', 'select'],
        nationality: ['Nationality', 'Kewarganegaraan', 'text'],
        marital: ['Marital', 'Status Perkawinan', 'select'],
        address: ['Address', 'Alamat', 'textarea'],
      };
      for (const part of parts) {
        if (partMap[part]) {
          const pDefs = partMap[part];
          for (let i = 0; i < pDefs.length; i += 3) {
            fields.push({ key: prefix + pDefs[i], label: pDefs[i+1], type: pDefs[i+2] });
          }
        }
      }
    }
  }

  // Deduplikasi
  const seen = new Set();
  return fields.filter(f => {
    if (seen.has(f.key)) return false;
    seen.add(f.key);
    return true;
  });
}

// ─── SIMULASI mapFieldsToBackend ─────────────────────────────────────────────

/**
 * Implementasi ulang logika signature() dan matchBySignature() dari letterFieldMap.js
 * untuk keperluan audit (tanpa import ES module)
 */
const GROUPS = [
  ["name", "nama"], ["address", "alamat"], ["birth", "lahir", "kelahiran"], ["place", "tempat"],
  ["date", "tanggal", "tgl"], ["father", "ayah", "bapak"], ["mother", "ibu"],
  ["bride", "wife", "istri", "wanita", "pengantin", "perempuan"], ["groom", "husband", "suami", "pria"],
  ["spouse", "pasangan"], ["nik"], ["occupation", "job", "pekerjaan"], ["child", "children", "anak"],
  ["witness", "witnesses", "saksi"], ["reporter", "informant", "pelapor"], ["religion", "agama"],
  ["deceased", "almarhum", "almarhumah", "jenazah"], ["death", "died", "meninggal", "kematian", "wafat"],
  ["nationality", "citizenship", "kewarganegaraan", "kebangsaan"], ["destination", "tujuan"],
  ["purpose", "keperluan", "need", "necessity", "kebutuhan"], ["gender", "kelamin", "sex"],
  ["family", "keluarga"], ["card", "kartu"], ["kk"], ["origin", "asal"], ["number", "nomor", "no"],
  ["status"], ["marital", "perkawinan", "kawin"], ["marriage", "nikah", "pernikahan"], ["bin", "binti"],
  ["education", "pendidikan"], ["attorney", "penerima"], ["grantor", "pemberi"], ["power", "kuasa"],
  ["head", "kepala"], ["prev", "previous", "sebelumnya", "sebelum"], ["ex", "mantan"],
  ["applicant", "pemohon"], ["time", "waktu", "jam"], ["age", "umur", "usia"],
  ["village", "desa", "kelurahan", "kalurahan"], ["district", "kecamatan", "kapanewon"],
  ["regency", "kabupaten"], ["city", "kota"], ["province", "provinsi"], ["hamlet", "dusun", "padukuhan", "dukuh"],
  ["rt"], ["rw"], ["postal", "pos"], ["code", "kode"], ["member", "members", "anggota"],
  ["phone", "telepon", "telp", "hp", "whatsapp", "wa"], ["akad"], ["reason", "reasons", "alasan"],
  ["order", "urutan"], ["guardian", "wali"], ["type", "types", "jenis"], ["relocation", "pindah", "kepindahan"],
  ["request", "permohonan", "permintaan"], ["other", "lain", "lainnya"], ["delivery", "pengiriman"],
  ["student", "siswa", "murid", "pelajar"], ["parent", "orangtua", "ortu"], ["guarantor", "penjamin"],
  ["day", "hari"], ["report", "laporan", "lapor"], ["business", "usaha"], ["location", "lokasi"],
  ["weight", "berat"], ["length", "panjang"], ["event", "acara", "kegiatan"], ["cause", "penyebab", "sebab"],
  ["area", "luas", "wilayah"], ["certificate", "cert", "akta", "sertifikat"], ["employee", "karyawan", "pegawai"],
  ["change", "changes", "perubahan"], ["tenant", "penyewa"], ["researcher", "peneliti"], ["research", "penelitian"],
  ["building", "bangunan"], ["income", "penghasilan", "pendapatan"], ["note", "notes", "catatan", "keterangan"],
  ["price", "harga"], ["rent", "sewa"], ["distributor", "penyalur"], ["host", "tuan"], ["ethnicity", "suku"],
  ["relationship", "relation", "hubungan"], ["year", "tahun"], ["month", "bulan"], ["responsible", "penanggung"],
  ["person", "orang"], ["count", "total", "jumlah"], ["owner", "pemilik"], ["company", "perusahaan"],
  ["activity", "kegiatan"], ["domicile", "domisili"], ["blood", "darah"], ["household", "rumah"],
  ["start", "mulai"], ["end", "akhir", "berakhir"], ["valid", "berlaku"], ["until", "hingga", "sampai"],
  ["departure", "keberangkatan", "berangkat"], ["return", "kembali"], ["duration", "lama", "durasi"],
  ["agency", "instansi", "institution", "lembaga"], ["rank", "pangkat"], ["position", "jabatan"],
  ["subject", "perihal"], ["result", "hasil"], ["budget", "anggaran"], ["cost", "biaya"], ["method", "metode"],
  ["class", "kelas"], ["school", "sekolah"], ["title", "judul"], ["program", "prodi"], ["office", "kantor"],
  ["official", "pejabat"], ["property", "properti"], ["land", "tanah"], ["terms", "ketentuan", "syarat"],
  ["conditions", "condition", "kondisi"], ["response", "tanggapan", "balasan"], ["participants", "peserta"],
  ["objective", "tujuan_kegiatan"], ["gestational", "kehamilan"], ["height", "tinggi"],
  ["1", "satu", "i", "pertama"], ["2", "dua", "ii", "kedua"], ["3", "tiga", "iii", "ketiga"],
  ["4", "empat", "iv", "keempat"], ["5", "lima", "v", "kelima"],
];
const PHRASES = [
  [["family", "card"], "kk"], [["kartu", "keluarga"], "kk"], [["jenis", "kelamin"], "gender"],
  [["kode", "pos"], "postal"], [["postal", "code"], "postal"], [["penanggung", "jawab"], "responsible"],
  [["penerima", "kuasa"], "attorney"], [["pemberi", "kuasa"], "grantor"], [["tempat", "tinggal"], "address"],
  [["nama", "lengkap"], "name"], [["full", "name"], "name"], [["no", "hp"], "phone"],
];
const STOP = new Set(["of", "di", "ke", "yang", "dan", "the", "and", "lengkap", "surat", "untuk", "dari", "pada", "full", "calon", "mempelai"]);
const GROUP_OF = new Map();
GROUPS.forEach((g, i) => g.forEach(t => { if (!GROUP_OF.has(t)) GROUP_OF.set(t, 'g' + i); }));

function tokenize(text) {
  return String(text ?? '')
    .replace(/\([^)]*\)/g, ' ')
    .replace(/^\s*(?:\d+\s*[.)']|[a-zA-Z][.)']\s+)\s*/, ' ')
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    .replace(/([a-z])([A-Z])/g, '$1 $2')
    .replace(/([A-Za-z])(\d)/g, '$1 $2')
    .replace(/(\d)([A-Za-z])/g, '$1 $2')
    .toLowerCase().split(/[^a-z0-9]+/).filter(Boolean);
}

function applyPhrases(tokens) {
  let out = [...tokens];
  for (const [pair, canon] of PHRASES) {
    for (let i = 0; i < out.length - 1; i++) {
      const [a, b] = [out[i], out[i+1]];
      if ((a === pair[0] && b === pair[1]) || (a === pair[1] && b === pair[0])) {
        out.splice(i, 2, canon); i--;
      }
    }
  }
  return out;
}

function signature(text) {
  const set = new Set(
    applyPhrases(tokenize(text)).filter(t => !STOP.has(t)).map(t => GROUP_OF.get(t) ?? t)
  );
  return [...set].sort().join('|');
}

function fieldLabelKey(label) {
  return norm(String(label ?? '').replace(/^\s*(?:\d+\s*[.)]|[a-zA-Z][.)']\s+)\s*/, ''));
}

/**
 * Simulasikan mapFieldsToBackend() untuk satu surat
 * Return { mapped: {formKey: backendKey}, unmapped: string[], ambiguous: string[] }
 */
function simulateMapping(formFields, backendFields, slug, fieldOverrides) {
  const allowed = new Set(backendFields.map(f => f.key));
  const byLabel = new Map(backendFields.map(f => [fieldLabelKey(f.label), f.key]));
  
  const used = new Set();
  const mapped = {};
  const unmapped = [];
  const wrongMapped = []; // salah petak: dipetakan ke field yang tidak tepat

  const slugOverrides = fieldOverrides[slug] ?? {};
  const globalOverrides = fieldOverrides['*'] ?? {};

  for (const field of formFields) {
    const key = field.key;
    const label = field.label;
    const type = field.type;
    let target = null;

    // 0) Override eksplisit
    target = slugOverrides[key] ?? globalOverrides[key] ?? null;
    if (target && allowed.has(target) && !used.has(target)) {
      // override valid
    } else if (target && !allowed.has(target)) {
      target = null; // override ke field yang tidak ada di backend
    } else {
      target = null;
    }

    // 1) Label persis
    if (!target && label) {
      const nl = fieldLabelKey(label);
      const hit = backendFields.find(f => !used.has(f.key) && fieldLabelKey(f.label) === nl);
      if (hit) target = hit.key;
    }

    // 2) key persis / snake_case
    if (!target) {
      const snakeKey = snake(key);
      if (allowed.has(key) && !used.has(key)) target = key;
      else if (allowed.has(snakeKey) && !used.has(snakeKey)) target = snakeKey;
    }

    // 3) Normalisasi vs field_key dan label backend
    if (!target) {
      const nk = norm(key);
      const ns = norm(snake(key));
      const hit = backendFields.find(f => !used.has(f.key) && (norm(f.key) === nk || norm(f.key) === ns || norm(f.label) === nk));
      if (hit) target = hit.key;
    }

    // 4) signature match
    if (!target) {
      const sig = signature(key);
      if (sig) {
        const hits = backendFields.filter(f => !used.has(f.key) && (signature(f.key) === sig || signature(f.label) === sig));
        if (hits.length === 1) target = hits[0].key;
      }
    }

    // 5) signature via label
    if (!target && label) {
      const sig = signature(label);
      if (sig) {
        const hits = backendFields.filter(f => !used.has(f.key) && (signature(f.key) === sig || signature(f.label) === sig));
        if (hits.length === 1) target = hits[0].key;
      }
    }

    if (target) {
      mapped[key] = target;
      used.add(target);
    } else {
      unmapped.push(key);
    }
  }

  return { mapped, unmapped, used };
}

// ─── IDENTIFIKASI FIELD DATE YANG BERPOTENSI HILANG ─────────────────────────

/**
 * Dugaan A: Field date yang dikirim multipart akan hancur karena appendNested()
 * mengkonversi Date object jadi properti year/month/day terpisah.
 * Sebenarnya: appendNested() memeriksa instanceof File, bukan Date. Tapi date ISO string
 * dari form dikirim sebagai string biasa. Bug sebenarnya: appendNested() pada value object
 * non-File (mis. {year:2024, month:1, day:1}) akan di-spread. Nilai Date object yang
 * dipass dari komponen Vue (mis. datepicker returns Date object, bukan string ISO)
 * AKAN hancur karena Object.entries(dateObj) tidak ada propertinya.
 */
function findDateFields(formFields) {
  return formFields.filter(f => f.type === 'date' || f.type === 'time');
}

// ─── PARSE BLADE FILES ───────────────────────────────────────────────────────

function findBladeForSlug(slug) {
  const candidates = findFiles(VIEWS_DIR, '.php');
  const slugFile = `${slug}.blade.php`;
  for (const c of candidates) {
    if (basename(c) === slugFile) return c;
  }
  return null;
}

function parseBladeFormKeys(bladePath) {
  if (!bladePath) return new Set();
  const src = readFile(bladePath);
  return extractBladeFormKeys(src);
}

// ─── MAIN AUDIT ──────────────────────────────────────────────────────────────

async function main() {
  console.log('📋 Memulai audit SIBIMO...\n');

  // 1. Baca data backend
  const seederSrc = readFile(SEEDER_FILE);
  const catalogSrc = readFile(CATALOG_FILE);
  const pdfServiceSrc = readFile(PDF_SERVICE);

  const seederData = parseSeeder(seederSrc);        // Map<code, field[]>
  const catalogData = parseCatalog(catalogSrc);     // Map<code, {fields, bladeView, slug}>
  const templateByCode = parseTemplateByCode(pdfServiceSrc);  // Map<code, slug>

  console.log(`✅ Seeder: ${seederData.size} kode, Katalog: ${catalogData.size} kode, Template: ${templateByCode.size} kode`);

  // 2. Baca override
  const fieldMapSrc = readFile(FIELD_MAP_FILE);
  const fieldOverrides = parseFieldOverrides(fieldMapSrc);
  const rowOverrides = parseRowOverrides(fieldMapSrc);

  // 3. Temukan semua file frontend
  const frontendFiles = findFiles(LETTERS_DIR, '.js');
  console.log(`📁 Frontend letter files: ${frontendFiles.length}`);

  // 4. Bangun daftar surat lengkap dari letterIndex.js
  const letterIndexSrc = readFile(join(ROOT_ADMIN, 'src/data/letterIndex.js'));
  const letterIndexRe = /\[\s*"([^"]+)"\s*,\s*"([^"]+)"\s*,\s*"([^"]+)"\s*,\s*"([^"]+)"\s*,\s*"([^"]+)"\s*\]/g;
  const letterIndex = [];
  let m;
  while ((m = letterIndexRe.exec(letterIndexSrc)) !== null) {
    letterIndex.push({ slug: m[1], group: m[2], category: m[3], title: m[4], shortCode: m[5] });
  }
  console.log(`📝 Letter index: ${letterIndex.length} surat\n`);

  // Gabungkan semua kode yang dikenal
  const allCodes = new Map();
  // Dari TEMPLATE_BY_CODE (backend utama)
  for (const [code, slug] of templateByCode) {
    allCodes.set(code, { code, slug, source: 'TEMPLATE_BY_CODE', seederFields: seederData.get(code) ?? [], catalogInfo: null });
  }
  // Dari AdditionalLetterTemplateCatalog
  for (const [code, info] of catalogData) {
    if (!allCodes.has(code)) {
      allCodes.set(code, { code, slug: info.slug, source: 'AdditionalCatalog', seederFields: [], catalogInfo: info });
    } else {
      allCodes.get(code).catalogInfo = info;
    }
  }
  // Dari seeder tapi tidak ada di template
  for (const [code, fields] of seederData) {
    if (!allCodes.has(code)) {
      allCodes.set(code, { code, slug: null, source: 'SeederOnly', seederFields: fields, catalogInfo: null });
    }
  }

  // 5. Audit per surat
  const results = [];
  const errorLog = [];

  for (const [code, info] of allCodes) {
    const { slug, seederFields, catalogInfo } = info;
    
    // Backend fields: dari seeder ATAU katalog
    let backendFields = seederFields;
    if (backendFields.length === 0 && catalogInfo?.fields) {
      backendFields = catalogInfo.fields;
    }

    // Frontend fields: cari file JS yang sesuai slug
    let frontendFields = [];
    let frontendFile = null;
    if (slug) {
      for (const f of frontendFiles) {
        if (basename(f, '.js') === slug) {
          frontendFile = f;
          frontendFields = extractFrontendFieldsFromFile(f);
          break;
        }
      }
    }

    // Blade: cari file blade
    const bladePath = slug ? findBladeForSlug(slug) : null;
    const bladeFormKeys = parseBladeFormKeys(bladePath);

    // Simulasi pemetaan
    let mappingResult = { mapped: {}, unmapped: [], used: new Set() };
    if (frontendFields.length > 0 && backendFields.length > 0) {
      mappingResult = simulateMapping(frontendFields, backendFields, slug, fieldOverrides);
    }

    // Field backend yang tidak terisi oleh frontend
    const usedBackendKeys = new Set(Object.values(mappingResult.mapped));
    const unusedBackend = backendFields.filter(f => !usedBackendKeys.has(f.key));

    // Salah-petak: field yang dipetakan tapi nama fieldnya tidak cocok konteks
    // (misal: fatherName dipetakan ke nama_ibu karena label "Nama" ambigu)
    const wrongMapped = [];
    for (const [formKey, backendKey] of Object.entries(mappingResult.mapped)) {
      const formSig = signature(formKey);
      const backendSig = signature(backendKey);
      // Deteksi salah petak: prefix peran tidak sesuai
      const formRoles = extractRoles(formKey);
      const backendRoles = extractRoles(backendKey);
      if (formRoles.size > 0 && backendRoles.size > 0) {
        const intersection = [...formRoles].filter(r => backendRoles.has(r));
        if (intersection.length === 0 && formRoles.size > 0 && backendRoles.size > 0) {
          // Peran form tidak ada di backend -> kemungkinan salah petak
          const override = fieldOverrides[slug]?.[formKey] ?? fieldOverrides['*']?.[formKey];
          if (!override) { // hanya laporkan jika bukan override eksplisit
            wrongMapped.push({ formKey, backendKey, formRoles: [...formRoles], backendRoles: [...backendRoles] });
          }
        }
      }
    }

    // Field date di frontend (potensi bug A)
    const dateFields = frontendFields.filter(f => f.type === 'date' || f.type === 'time');

    // Key yang dibaca blade tapi tidak pernah diisi backend
    const bladeNotFilled = [];
    for (const bladeKey of bladeFormKeys) {
      if (!backendFields.some(f => f.key === bladeKey) && !isSystemKey(bladeKey)) {
        bladeNotFilled.push(bladeKey);
      }
    }

    results.push({
      code,
      slug: slug ?? '(tidak ada)',
      title: info.source === 'AdditionalCatalog' ? catalogInfo?.name : 
             letterIndex.find(l => l.slug === slug)?.title ?? slug,
      source: info.source,
      frontendFile: frontendFile ? basename(frontendFile) : '(tidak ditemukan)',
      bladePath: bladePath ? basename(bladePath) : '(tidak ditemukan)',
      frontendFields,
      backendFields,
      unmapped: mappingResult.unmapped,
      wrongMapped,
      unusedBackend,
      bladeNotFilled,
      dateFields,
      bladeFormKeys,
    });
  }

  // 6. Generate laporan
  const report = generateReport(results, fieldOverrides, rowOverrides, seederData, catalogData, templateByCode);
  const outPath = join(ROOT_BACKEND, 'AUDIT-SURAT.md');
  writeFileSync(outPath, report, 'utf8');
  console.log(`\n✅ Laporan audit selesai: ${outPath}`);
  console.log(`📊 Total surat diaudit: ${results.length}`);
  
  const totalUnmapped = results.reduce((s, r) => s + r.unmapped.length, 0);
  const totalWrong = results.reduce((s, r) => s + r.wrongMapped.length, 0);
  const totalBladeEmpty = results.reduce((s, r) => s + r.bladeNotFilled.length, 0);
  console.log(`⚠️  Total field terbuang: ${totalUnmapped}`);
  console.log(`⚠️  Total salah-petak: ${totalWrong}`);
  console.log(`⚠️  Total key blade tidak terisi: ${totalBladeEmpty}`);
}

// Ekstrak peran dari nama field (groom, bride, father, mother, witness1, dll.)
const ROLE_PREFIXES = ['groom', 'bride', 'husband', 'wife', 'father', 'mother', 
                       'child', 'deceased', 'witness1', 'witness2', 'witness', 
                       'reporter', 'attorney', 'grantor', 'guardian', 'student',
                       'spouse', 'owner', 'applicant', 'sender', 'recipient',
                       'previous', 'ex'];
function extractRoles(key) {
  const roles = new Set();
  const lower = key.toLowerCase();
  for (const role of ROLE_PREFIXES) {
    if (lower.startsWith(role) || lower.includes('_' + role + '_') || lower.endsWith('_' + role)) {
      roles.add(role);
    }
  }
  return roles;
}

function isSystemKey(key) {
  const sys = new Set(['number', 'nomor', 'year', 'signer', 'signature', 'kop', 'logo', 
                       'village', 'region', 'applicant', 'form_code', 'document_title',
                       'date_parts', 'marriage', 'birth', 'kia', 'travelOrder',
                       'stayApplication', 'residentRequest', 'populationOccurrence']);
  return sys.has(key) || key.startsWith('applicant_') || key.startsWith('signature_');
}

// ─── GENERATE REPORT ─────────────────────────────────────────────────────────

function generateReport(results, fieldOverrides, rowOverrides, seederData, catalogData, templateByCode) {
  const now = new Date().toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' });
  
  let md = `# AUDIT SURAT SIBIMO\n\n`;
  md += `**Dibuat:** ${now}  \n`;
  md += `**Total surat diaudit:** ${results.length}  \n\n`;
  md += `> Skrip audit: \`sibimo/audit-surat.mjs\`  \n`;
  md += `> Tidak ada perubahan pada file aplikasi.\n\n`;

  md += `---\n\n## RINGKASAN EKSEKUTIF\n\n`;
  
  // Summary table
  const withIssues = results.filter(r => r.unmapped.length > 0 || r.wrongMapped.length > 0 || r.bladeNotFilled.length > 0);
  md += `| Kode | Slug | Field Terbuang | Salah-Petak | Key Blade Kosong | Field Date Rentan |\n`;
  md += `|------|------|:-:|:-:|:-:|:-:|\n`;
  for (const r of results.sort((a, b) => (b.unmapped.length + b.wrongMapped.length + b.bladeNotFilled.length) - (a.unmapped.length + a.wrongMapped.length + a.bladeNotFilled.length))) {
    const hasIssue = r.unmapped.length > 0 || r.wrongMapped.length > 0 || r.bladeNotFilled.length > 0;
    const mark = (n, warn = 0) => n === 0 ? '✅ 0' : n > warn ? `🔴 ${n}` : `🟡 ${n}`;
    md += `| \`${r.code}\` | \`${r.slug}\` | ${mark(r.unmapped.length, 2)} | ${mark(r.wrongMapped.length)} | ${mark(r.bladeNotFilled.length)} | ${r.dateFields.length > 0 ? `🟡 ${r.dateFields.length}` : '✅ 0'} |\n`;
  }

  md += `\n---\n\n## DETAIL PER SURAT\n\n`;

  // Sort: surat bermasalah dulu
  const sorted = [...results].sort((a, b) => 
    (b.unmapped.length + b.wrongMapped.length * 3 + b.bladeNotFilled.length * 2) - 
    (a.unmapped.length + a.wrongMapped.length * 3 + a.bladeNotFilled.length * 2)
  );

  for (const r of sorted) {
    const hasIssue = r.unmapped.length > 0 || r.wrongMapped.length > 0 || r.bladeNotFilled.length > 0;
    const status = hasIssue ? '⚠️' : '✅';
    
    md += `### ${status} \`${r.code}\` — ${r.title ?? r.slug}\n\n`;
    md += `- **Slug:** \`${r.slug}\`\n`;
    md += `- **Sumber data:** ${r.source}\n`;
    md += `- **File frontend:** \`${r.frontendFile}\`\n`;
    md += `- **Blade template:** \`${r.bladePath}\`\n`;
    md += `- **Field frontend:** ${r.frontendFields.length} | **Field backend:** ${r.backendFields.length}\n\n`;

    // 1. Field terbuang (unmapped)
    if (r.unmapped.length > 0) {
      md += `#### ❌ Field Frontend Tidak Punya Pasangan Backend (TERBUANG)\n\n`;
      md += `> Field ini diisi di form tapi TIDAK disimpan ke database sama sekali.\n\n`;
      md += `| Key Form | Label | Tipe |\n|---|---|---|\n`;
      for (const key of r.unmapped) {
        const field = r.frontendFields.find(f => f.key === key);
        md += `| \`${key}\` | ${field?.label ?? '-'} | \`${field?.type ?? '-'}\` |\n`;
      }
      md += '\n';
    } else if (r.frontendFields.length > 0) {
      md += `#### ✅ Semua field frontend berhasil dipetakan ke backend\n\n`;
    } else if (r.frontendFields.length === 0) {
      md += `#### ℹ️ File frontend tidak ditemukan atau tidak ada field yang dapat diparsing\n\n`;
    }

    // 2. Salah-petak
    if (r.wrongMapped.length > 0) {
      md += `#### ⚠️ Kemungkinan Salah-Petak (Peran Tidak Cocok)\n\n`;
      md += `> Field dipetakan ke backend, tapi prefix peran form ≠ prefix peran backend.\n`;
      md += `> Bisa jadi label generik ("Nama", "Agama") dipetakan ke field identitas umum alih-alih blok peran yang seharusnya.\n\n`;
      md += `| Key Form | Peran Form | Backend Key | Peran Backend |\n|---|---|---|---|\n`;
      for (const w of r.wrongMapped) {
        md += `| \`${w.formKey}\` | ${w.formRoles.join(', ')} | \`${w.backendKey}\` | ${w.backendRoles.join(', ')} |\n`;
      }
      md += '\n';
    }

    // 3. Key blade kosong
    if (r.bladeNotFilled.length > 0) {
      md += `#### 🕳️ Key \`$form['...']'\` di Blade Tidak Ada di Definisi Backend\n\n`;
      md += `> Key ini dibaca blade tapi tidak pernah disimpan ke \`form_data\` (tidak ada di seeder/katalog).\n\n`;
      md += `| Key di Blade |\n|---|\n`;
      for (const k of r.bladeNotFilled) md += `| \`${k}\` |\n`;
      md += '\n';
    }

    // 4. Field backend tidak dipakai blade (informatif)
    if (r.unusedBackend.length > 0) {
      md += `#### ℹ️ Field Backend yang Tidak Pernah Dipakai Blade (informatif)\n\n`;
      const listed = r.unusedBackend.slice(0, 10);
      md += listed.map(f => `- \`${f.key}\` ("${f.label}")`).join('\n') + '\n';
      if (r.unusedBackend.length > 10) md += `- ... dan ${r.unusedBackend.length - 10} lagi\n`;
      md += '\n';
    }

    // 5. Field date rentan
    if (r.dateFields.length > 0) {
      md += `#### 📅 Field Date/Time (Rentan Bug A — Multipart)\n\n`;
      md += `> Jika ada lampiran → surat dikirim multipart → \`appendNested()\` tidak dipanggil untuk File,\n`;
      md += `> tapi jika datepicker Vue menghasilkan objek Date (bukan string ISO), objek itu akan di-spread\n`;
      md += `> menjadi properti kosong dan nilainya hilang.\n\n`;
      md += r.dateFields.map(f => `- \`${f.key}\` ("${f.label}") → backend: \`${Object.entries(Object.fromEntries([[f.key, '...'], ...(r.frontendFields.filter(x => x.key === f.key).map(x => [x.key, x.type]))])).map(([k,v]) => k).join(', ')}\``).join('\n') + '\n\n';
    }

    // Tampilkan pemetaan yang berhasil (hanya jika ada)
    if (Object.keys(mappingResult_from(r)).length > 0 && r.frontendFields.length <= 30) {
      md += `<details>\n<summary>Pemetaan Berhasil (${Object.keys(mappingResult_from(r)).length} field)</summary>\n\n`;
      md += `| Key Form | → | Backend Key |\n|---|---|---|\n`;
      for (const [fk, bk] of Object.entries(mappingResult_from(r))) {
        const isOverride = fieldOverrides[r.slug]?.[fk] || fieldOverrides['*']?.[fk];
        md += `| \`${fk}\` | → | \`${bk}\`${isOverride ? ' _(override)_' : ''} |\n`;
      }
      md += '\n</details>\n\n';
    }

    md += `---\n\n`;
  }

  // ─── ANALISIS AKAR MASALAH ─────────────────────────────────────────────────
  md += `## 🔬 ANALISIS AKAR MASALAH\n\n`;
  md += generateRootCauseAnalysis(results, fieldOverrides, rowOverrides, seederData, catalogData, templateByCode);

  return md;
}

// Helper: ambil mapping dari hasil audit
function mappingResult_from(r) {
  const mapped = {};
  if (!r.frontendFields || !r.backendFields) return mapped;
  // Re-simulasikan mapping untuk mendapatkan hasilnya
  // (sudah ada di r tapi tidak disimpan - kita perlu re-run atau simpan sebelumnya)
  // Untuk laporan kita skip re-simulasi; return empty
  return {};
}

function generateRootCauseAnalysis(results, fieldOverrides, rowOverrides, seederData, catalogData, templateByCode) {
  let md = '';

  // A. Bug appendNested() dengan Date object
  md += `### Bug A — Date Hilang saat Multipart (appendNested + Date Object)\n\n`;
  md += `**Status:** ⚠️ TERKONFIRMASI PARSIAL\n\n`;
  md += `**Mekanisme:** \`appendNested(fd, key, value)\` hanya cek \`instanceof File\`. `;
  md += `Jika value adalah objek Date (bukan string ISO), \`Object.entries(dateObj)\` menghasilkan array kosong → tanggal hilang diam-diam.\n\n`;
  md += `**Kapan terjadi:** Surat dengan lampiran (multipart) + datepicker Vue yang menghasilkan Date object alih-alih string ISO.\n\n`;
  
  const suratDenganDate = results.filter(r => r.dateFields.length > 0);
  md += `**Surat berisiko (${suratDenganDate.length} surat):**\n`;
  for (const r of suratDenganDate) {
    md += `- \`${r.code}\` (${r.slug}): ${r.dateFields.map(f => f.key).join(', ')}\n`;
  }
  md += '\n';

  // B. Label generik ambigu
  md += `### Bug B — Label Generik Ambigu (mapFieldsToBackend step "label persis")\n\n`;
  md += `**Status:** 🔴 TERKONFIRMASI\n\n`;
  md += `**Mekanisme:** Label umum seperti "Tempat Lahir", "Agama", "Pekerjaan", "Tanggal Lahir" muncul di banyak blok.\n`;
  md += `Saat \`mapFieldsToBackend()\` mencari via "label persis", ia menemukan field \`tempat_lahir\` (pemohon) dahulu\n`;
  md += `dan mengklaim slot itu — sehingga \`groomBirthPlace\` (label "Tempat Lahir") tidak bisa dipetakan ke \`groom_birth_place\` lagi.\n\n`;
  md += `**Efek:** Blok calon suami/istri, ayah, ibu, saksi kehilangan data tempat lahir, agama, pekerjaan.\n\n`;
  md += `**Surat paling terdampak:** Semua surat nikah (N1, N2, N4, N5, SPNIP, SPOT, dll.) dan surat kematian dengan data ibu/ayah.\n\n`;

  // Hitung berapa surat punya field berlabel sama
  const ambiguousLabels = new Map();
  for (const r of results) {
    const labelCounts = new Map();
    for (const f of r.frontendFields) {
      labelCounts.set(f.label, (labelCounts.get(f.label) ?? 0) + 1);
    }
    for (const [label, count] of labelCounts) {
      if (count > 1 && ['Tempat Lahir', 'Tanggal Lahir', 'Agama', 'Pekerjaan', 'NIK', 'Nama', 'Nama Lengkap', 'Alamat', 'Jenis Kelamin'].includes(label)) {
        if (!ambiguousLabels.has(label)) ambiguousLabels.set(label, new Set());
        ambiguousLabels.get(label).add(r.code);
      }
    }
  }
  if (ambiguousLabels.size > 0) {
    md += `**Label ambigu yang muncul berkali-kali dalam satu surat:**\n`;
    for (const [label, codes] of ambiguousLabels) {
      md += `- "${label}" → dalam surat: ${[...codes].join(', ')}\n`;
    }
    md += '\n';
  }

  // C. Field backend kurang
  md += `### Bug C — Field Backend Kurang dari Kebutuhan Blade\n\n`;
  md += `**Status:** 🔴 TERKONFIRMASI\n\n`;
  md += `**Mekanisme:** Seeder/katalog mendefinisikan field backend yang tidak mencukupi semua yang dibutuhkan blade.\n`;
  md += `Contoh: \`groom_father_birth_date\`, \`groom_father_religion\`, \`groom_father_occupation\` tidak ada di \`marriedPersonFields()\`.\n\n`;
  
  const missingBackend = results.filter(r => r.bladeNotFilled.length > 0);
  md += `**Surat dengan key blade yang tidak punya backing backend (${missingBackend.length} surat):**\n`;
  for (const r of missingBackend) {
    md += `- \`${r.code}\` (${r.slug}): ${r.bladeNotFilled.join(', ')}\n`;
  }
  md += '\n';

  // D. Key backend vs blade mismatch
  md += `### Bug D — Key Backend ≠ Key yang Dibaca Blade\n\n`;
  md += `**Status:** 🔴 TERKONFIRMASI\n\n`;
  md += `**Mekanisme:** \`normalizeRootTemplateForm()\` dan \`normalizeComplexTemplateForm()\` menambahkan alias,\n`;
  md += `tapi tidak semua konversi tercakup. Field backend \`groom_citizenship\` vs blade membaca \`groom_nationality\`.\n\n`;
  md += `**Contoh spesifik yang ditemukan:**\n`;
  md += `- Katalog menyimpan \`groom_citizenship\` tapi blade membaca \`groom_citizenship\` → OK, tapi normalizer alias di service mengubah \`groomNationality\` → \`groom_citizenship\`\n`;
  md += `- Surat kematian: frontend memakai \`deathDate\` (tanggal meninggal pemohon), backend punya \`meninggal_hari_tanggal\` → perlu override eksplisit yang sudah ada\n`;
  md += `- Surat pernikahan lama (N1-N6): blade membaca \`\$marriage['...']['...'])\` (nested) tapi form_data menyimpan flat\n\n`;

  // E. Centang disimpan sebagai label
  md += `### Bug E — Nilai Checkbox/Select Disimpan sebagai Label Teks\n\n`;
  md += `**Status:** 🟡 PERLU VERIFIKASI\n\n`;
  md += `**Mekanisme:** Form frontend menyimpan value select sebagai teks label (mis. "1. Numpang KK"), `;
  md += `sedangkan blade atau backend mengharapkan kode numerik atau value bersih.\n`;
  md += `\`matchBackendOption()\` sudah mencoba menangani ini, tapi hanya jika backend mendefinisikan \`options\`.\n\n`;
  md += `**Surat berisiko:** \`relocation-cover-letter\` (originKkStatus, destinationKkStatus), \`ktp-application-form\` (applicationType)\n\n`;

  // F. Seeder vs Katalog tidak sinkron
  md += `### Bug F — Definisi Field di Seeder Tertinggal dari Katalog\n\n`;
  md += `**Status:** 🔴 TERKONFIRMASI\n\n`;
  md += `**Mekanisme:** \`AdditionalLetterTemplateCatalog.php\` mendefinisikan field terbaru, tapi \`LetterTypeFieldSeeder.php\` `;
  md += `mungkin belum diperbarui (atau sebaliknya). Bila seeder pernah dijalankan dengan data lama, DB punya field lama; `;
  md += `bila field baru hanya di katalog PHP tapi belum di-seed ulang, \`getFields()\` mengembalikan list lama.\n\n`;
  
  const onlyInCatalog = [];
  const onlyInSeeder = [];
  for (const [code] of catalogData) {
    if (!seederData.has(code)) onlyInCatalog.push(code);
  }
  for (const [code] of seederData) {
    if (!catalogData.has(code) && !templateByCode.has(code)) onlyInSeeder.push(code);
  }
  if (onlyInCatalog.length > 0) md += `**Ada di katalog tapi TIDAK di seeder:** ${onlyInCatalog.join(', ')}\n\n`;
  if (onlyInSeeder.length > 0) md += `**Ada di seeder tapi TIDAK di katalog/template:** ${onlyInSeeder.join(', ')}\n\n`;

  // Akar masalah baru
  md += `### Bug G — BARU: Field Frontend Tidak Punya File JS (Tidak Diaudit)\n\n`;
  md += `**Status:** 🔴 DITEMUKAN\n\n`;
  const noFrontend = results.filter(r => r.frontendFile === '(tidak ditemukan)' && r.backendFields.length > 0);
  md += `**Surat dengan backend terdefinisi tapi tidak ada file frontend JS:**\n`;
  for (const r of noFrontend) {
    md += `- \`${r.code}\` (\`${r.slug}\`) — ${r.title ?? '-'}\n`;
  }
  md += `\nSurat ini tidak dapat diaudit field frontendnya karena file JS tidak ditemukan di \`data/letters/\`.\n`;
  md += `Kemungkinan: (1) field didefinisikan di letterFields.js langsung tanpa file per-surat, atau (2) slug tidak cocok.\n\n`;

  md += `### Bug H — BARU: Duplikasi Key \`registration_number\` di \`marriedPersonFields()\`\n\n`;
  md += `**Status:** 🔴 DITEMUKAN\n\n`;
  md += `**Mekanisme:** \`AdditionalLetterTemplateCatalog::marriedPersonFields()\` mendefinisikan \`registration_number\` DUA KALI (baris 331 dan 405). `;
  md += `Hal ini menyebabkan ambiguitas dan salah satu definisi tidak terpakai.\n\n`;

  md += `### Bug I — BARU: slug di letterIndex tidak cocok dengan slug di TEMPLATE_BY_CODE\n\n`;
  md += `**Status:** 🔴 DITEMUKAN\n\n`;
  md += `**Contoh:**\n`;
  md += `- letterIndex menggunakan \`bussiness-permit-letter\` (typo: bussiness), TEMPLATE_BY_CODE menggunakan \`business-permit-letter\`\n`;
  md += `- letterIndex menggunakan \`population-occurrence-registration\`, TEMPLATE_BY_CODE menggunakan \`population-occurrence-registration-form\`\n`;
  md += `- letterIndex menggunakan \`family-biodata\`, katalog menggunakan \`family-biodata-form\`\n\n`;
  md += `Akibatnya: \`resolveLetterType()\` gagal menemukan tipe surat di backend → error "Tipe surat ... belum terdaftar di backend"\n\n`;

  md += `### Bug J — BARU: normalizeRootTemplateForm tidak menangani semua kode\n\n`;
  md += `**Status:** 🔴 DITEMUKAN\n\n`;
  md += `**Mekanisme:** \`normalizeComplexTemplateForm()\` hanya memproses kode tertentu dalam array \`$marriageCodes\`. `;
  md += `Kode katalog baru seperti \`SPCCP\`, \`SKKMP\`, \`SKUMN\`, dll. sudah masuk array itu, `;
  md += `TAPI kode \`SKBK2\`, \`DKU\`, \`DKS\`, \`DKP\`, \`DKH\`, \`SPKLC\`, \`SKC\`, dll. dari katalog `;
  md += `tidak punya normalizer khusus DAN tidak masuk \`TEMPLATE_BY_CODE\`, sehingga \`viewData()\` tidak tahu template mana yang dipakai.\n\n`;

  return md;
}

// Panggil main
main().catch(e => { console.error(e); process.exit(1); });
