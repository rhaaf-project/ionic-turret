"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
// Trader Turret - Electron Preload Script
const electron_1 = require("electron");
// Expose PTT IPC to renderer
electron_1.contextBridge.exposeInMainWorld('electronAPI', {
    // PTT controls
    onPttKeyDown: (callback) => {
        electron_1.ipcRenderer.on('ptt-key-down', (_event, key) => callback(key));
    },
    activatePtt: (channelKey) => {
        electron_1.ipcRenderer.send('ptt-activate', channelKey);
    },
    deactivatePtt: (channelKey) => {
        electron_1.ipcRenderer.send('ptt-deactivate', channelKey);
    },
    // Check if running in Electron
    isElectron: true
});
