"use strict";
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || (function () {
    var ownKeys = function(o) {
        ownKeys = Object.getOwnPropertyNames || function (o) {
            var ar = [];
            for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
            return ar;
        };
        return ownKeys(o);
    };
    return function (mod) {
        if (mod && mod.__esModule) return mod;
        var result = {};
        if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
        __setModuleDefault(result, mod);
        return result;
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
// Trader Turret - Electron Main Process
const electron_1 = require("electron");
const path = __importStar(require("path"));
// [FIX] Ignore certificate errors for self-signed PBX SSL (MUST be before app.ready)
electron_1.app.commandLine.appendSwitch('ignore-certificate-errors');
electron_1.app.commandLine.appendSwitch('allow-insecure-localhost');
let mainWindow = null;
function createWindow() {
    // Create the browser window with single-window sidebar layout
    mainWindow = new electron_1.BrowserWindow({
        width: 1400,
        height: 900,
        minWidth: 1024,
        minHeight: 768,
        title: 'Trader Turret',
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, 'preload.js'),
            webviewTag: true // Enable webview for WhatsApp/Teams
        }
    });
    // Load the Ionic app (built Angular app)
    if (process.env['NODE_ENV'] === 'development') {
        mainWindow.loadURL('http://localhost:4200');
        mainWindow.webContents.openDevTools();
    }
    else {
        // Path from electron/dist/main.js to www/index.html
        mainWindow.loadFile(path.join(__dirname, '../../www/index.html'));
    }
    // Register Global Hotkeys for PTT
    registerPttHotkeys();
    mainWindow.on('closed', () => {
        mainWindow = null;
    });
}
function registerPttHotkeys() {
    // Register Alt+P as PTT trigger (works even when app is unfocused)
    // Note: globalShortcut requires modifier+key combo, can't use single key like 'Alt'
    const registered = electron_1.globalShortcut.register('Alt+P', () => {
        if (mainWindow) {
            mainWindow.webContents.send('ptt-key-down', 'Alt+P');
        }
    });
    if (!registered) {
        console.log('[PTT] Failed to register Alt+P shortcut');
    }
    else {
        console.log('[PTT] Alt+P registered as PTT hotkey');
    }
    // Note: For true PTT with keydown/keyup, consider using 'uiohook-napi' package
    // globalShortcut only fires on keydown, not keyup
}
// IPC handlers for renderer communication
electron_1.ipcMain.on('ptt-activate', (event, channelKey) => {
    console.log(`PTT activated for channel: ${channelKey}`);
});
electron_1.ipcMain.on('ptt-deactivate', (event, channelKey) => {
    console.log(`PTT deactivated for channel: ${channelKey}`);
});
// Setup persistent sessions for webviews
function setupWebviewSessions() {
    // WhatsApp session
    electron_1.session.fromPartition('persist:whatsapp');
    // Teams session
    electron_1.session.fromPartition('persist:teams');
}
electron_1.app.whenReady().then(() => {
    setupWebviewSessions();
    createWindow();
    // [FIX] Bypass SSL certificate errors for self-signed PBX certs (dev only)
    electron_1.app.on('certificate-error', (event, webContents, url, error, certificate, callback) => {
        console.log('[SSL] Certificate error for:', url, error);
        // Bypass certificate validation for PBX WSS connection
        if (url.includes('103.154.80.172') || url.includes('wss://')) {
            event.preventDefault();
            callback(true); // Trust this certificate
        }
        else {
            callback(false);
        }
    });
    electron_1.app.on('activate', () => {
        if (electron_1.BrowserWindow.getAllWindows().length === 0) {
            createWindow();
        }
    });
});
electron_1.app.on('window-all-closed', () => {
    electron_1.globalShortcut.unregisterAll();
    if (process.platform !== 'darwin') {
        electron_1.app.quit();
    }
});
electron_1.app.on('will-quit', () => {
    electron_1.globalShortcut.unregisterAll();
});
