import { copyFile, mkdir, readdir, writeFile } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { rolldown } from 'rolldown';
import * as sass from 'sass';
import postcss from 'postcss';
import postcssImport from 'postcss-import';
import autoprefixer from 'autoprefixer';
import cssnano from 'cssnano';
import { watch } from 'chokidar';

const root = path.dirname(fileURLToPath(import.meta.url));
const output = path.resolve(root, process.env.BUILD_DIR || 'dist');
const args = new Set(process.argv.slice(2));
const debug = args.has('--debug');
const buildCss = !args.has('--js');
const buildJs = !args.has('--css');

async function styles() {
    const source = path.join(root, 'src/scss');
    const destination = path.join(output, 'css');
    await mkdir(destination, { recursive: true });
    const images = path.join(output, 'images');
    await mkdir(images, { recursive: true });
    for (const file of ['flags.webp', 'flags@2x.webp']) {
        await copyFile(path.join(root, 'node_modules/intl-tel-input/dist/img', file), path.join(images, file));
    }
    for (const file of await readdir(source)) {
        if (!file.endsWith('.scss') || file.startsWith('_')) continue;
        const input = path.join(source, file);
        const target = path.join(destination, file.replace(/\.scss$/, '.css'));
        const result = await sass.compileAsync(input, { style: 'compressed', sourceMap: true });
        const css = await postcss([postcssImport(), autoprefixer({ cascade: false, supports: false }), cssnano()])
            .process(result.css, { from: input, to: target, map: { prev: result.sourceMap, inline: false } });
        await writeFile(target, css.css);
        await writeFile(`${target}.map`, css.map.toString());
    }
}

async function scripts() {
    const source = path.join(root, 'src/javascript');
    const destination = path.join(output, 'javascript');
    await mkdir(destination, { recursive: true });
    for (const file of await readdir(source)) {
        if (!file.endsWith('.js')) continue;
        const bundle = await rolldown({ input: path.join(source, file), platform: 'browser' });
        try {
            await bundle.write({ file: path.join(destination, file), format: 'es', sourcemap: true, minify: !debug });
        } finally {
            await bundle.close();
        }
    }
}

async function build() {
    if (buildCss) await styles();
    if (buildJs) await scripts();
}

await build();
if (args.has('--watch')) {
    let pending = Promise.resolve();
    watch(path.join(root, 'src'), { ignoreInitial: true }).on('all', () => {
        pending = pending.then(build).catch(error => console.error(error));
    });
    console.log('Watching client sources for changes');
}
