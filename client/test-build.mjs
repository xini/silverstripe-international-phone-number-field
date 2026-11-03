import assert from 'node:assert/strict';
import { execFileSync } from 'node:child_process';
import { mkdtemp, readFile, readdir, rm } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.dirname(fileURLToPath(import.meta.url));
const output = await mkdtemp(path.join(root, '.build-test-'));
try {
    execFileSync(process.execPath, [path.join(root, 'build.mjs')], {
        cwd: root,
        env: { ...process.env, BUILD_DIR: output },
        stdio: 'inherit'
    });
    const entries = (await readdir(path.join(root, 'src/javascript'))).filter(file => file.endsWith('.js'));
    assert.ok(entries.length > 0);
    for (const entry of entries) {
        const target = path.join(output, 'javascript', entry);
        assert.ok((await readFile(target, 'utf8')).length > 0);
        JSON.parse(await readFile(`${target}.map`, 'utf8'));
    }
    const target = path.join(output, 'css/intl-phone-number-field.css');
    const css = await readFile(target, 'utf8');
    assert.ok(css.includes('.iti__country-container'));
    for (const image of ['flags.webp', 'flags@2x.webp']) {
        assert.ok(css.includes(`../images/${image}`));
        assert.ok((await readFile(path.join(output, 'images', image))).length > 0);
    }
    assert.ok(!css.includes('@import'));
    assert.ok(!css.replace(/\/\*# sourceMappingURL=.*?\*\//, '').trim().includes('\n'), 'CSS should be minified');
    JSON.parse(await readFile(`${target}.map`, 'utf8'));
    console.log('Build smoke test passed');
} finally {
    await rm(output, { recursive: true, force: true });
}