import fs from "node:fs";
import path from "node:path";
import { pathToFileURL } from "node:url";

const adminRoot = "D:/Magang/sibimo-admin";
const { createServer } = await import(pathToFileURL(path.join(adminRoot, "node_modules/vite/dist/node/index.js")).href);
const vite = await createServer({ root: adminRoot, configFile: path.join(adminRoot, "vite.config.js"), server: { middlewareMode: true }, appType: "custom" });
const index = await vite.ssrLoadModule("/src/data/letterIndex.js");
const backendMap = await vite.ssrLoadModule("/src/data/letterBackendMap.js");

function collectFields(sections) {
  const rows = [];
  for (const section of sections ?? []) {
    for (const field of section.fields ?? []) {
      rows.push({
        key: field.key,
        label: field.label,
        type: field.type ?? "text",
        section: section.title,
        columns: field.columns ?? null,
      });
    }
  }
  return rows;
}

function slugToPath(slug) {
  const row = index.LETTER_INDEX.find((item) => item[0] === slug);
  return [row?.[1] ?? "", `${slug}.js`];
}

const letters = [];
for (const [slug, group, category, title, displayCode] of index.LETTER_INDEX) {
  const code = backendMap.BACKEND_CODE_BY_SLUG[slug];
  const modulePath = "/src/data/letters/" + slugToPath(slug).join("/");
  try {
    const mod = await vite.ssrLoadModule(modulePath);
    const fields = collectFields(mod.sections);
    letters.push({
      slug,
      group,
      category,
      title,
      displayCode,
      code: code ?? null,
      fields,
    });
  } catch (err) {
    letters.push({
      slug,
      group,
      category,
      title,
      displayCode,
      code: code ?? null,
      error: err.message,
    });
  }
}

await vite.close();
fs.writeFileSync("D:/Magang/sibimo/letter_forms.json", JSON.stringify(letters, null, 2));
console.log(`Extracted ${letters.length} letter forms to letter_forms.json`);
