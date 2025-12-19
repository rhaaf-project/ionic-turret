// Trader Turret - Electron Preload Script
import { contextBridge, ipcRenderer } from 'electron';

// Expose PTT IPC to renderer
contextBridge.exposeInMainWorld('electronAPI', {
    // PTT controls
    onPttKeyDown: (callback: (key: string) => void) => {
        ipcRenderer.on('ptt-key-down', (_event, key) => callback(key));
    },

    activatePtt: (channelKey: string) => {
        ipcRenderer.send('ptt-activate', channelKey);
    },

    deactivatePtt: (channelKey: string) => {
        ipcRenderer.send('ptt-deactivate', channelKey);
    },

    // Check if running in Electron
    isElectron: true
});
