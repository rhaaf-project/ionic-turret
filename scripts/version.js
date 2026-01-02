/**
 * Auto-generate version info from git
 * Run: node scripts/version.js
 */
const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

// Get git info
const getGitInfo = () => {
    try {
        const commitHash = execSync('git rev-parse --short HEAD').toString().trim();
        const commitDate = execSync('git log -1 --format=%ci').toString().trim();
        return { commitHash, commitDate };
    } catch (e) {
        return { commitHash: 'dev', commitDate: new Date().toISOString() };
    }
};

// Generate version string
const generateVersion = () => {
    const { commitHash, commitDate } = getGitInfo();
    const now = new Date();
    const dateStr = now.toLocaleDateString('en-CA'); // YYYY-MM-DD
    const timeStr = now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });

    return {
        version: `v.01.03`,
        buildTime: `${dateStr} ${timeStr}`,
        commitHash,
        commitDate,
        fullString: `v.01.03-${timeStr} [${commitHash}]`
    };
};

// Write to version.ts
const versionInfo = generateVersion();
const outputPath = path.join(__dirname, '../src/app/version.ts');

const content = `// Auto-generated - DO NOT EDIT
// Generated at: ${new Date().toISOString()}

export const VERSION = {
    version: '${versionInfo.version}',
    buildTime: '${versionInfo.buildTime}',
    commitHash: '${versionInfo.commitHash}',
    commitDate: '${versionInfo.commitDate}',
    fullString: '${versionInfo.fullString}'
};
`;

fs.writeFileSync(outputPath, content);
console.log('✅ Version info generated:', versionInfo.fullString);
