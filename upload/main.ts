// Trader Turret - Electron Main Process
import { app, BrowserWindow, globalShortcut, ipcMain, session } from 'electron';
import * as path from 'path';

// [FIX] Ignore certificate errors for self-signed PBX SSL (MUST be before app.ready)
app.commandLine.appendSwitch('ignore-certificate-errors');
app.commandLine.appendSwitch('allow-insecure-localhost');

let mainWindow: BrowserWindow | null = null;

function createWindow(): void {
    // Create the browser window with single-window sidebar layout
    mainWindow = new BrowserWindow({
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
    } else {
        // Path from electron/dist/main.js to www/index.html
        mainWindow.loadFile(path.join(__dirname, '../../www/index.html'));
    }

    // Register Global Hotkeys for PTT
    registerPttHotkeys();

    mainWindow.on('closed', () => {
        mainWindow = null;
    });
}

function registerPttHotkeys(): void {
    // Register Alt+P as PTT trigger (works even when app is unfocused)
    // Note: globalShortcut requires modifier+key combo, can't use single key like 'Alt'
    const registered = globalShortcut.register('Alt+P', () => {
        if (mainWindow) {
            mainWindow.webContents.send('ptt-key-down', 'Alt+P');
        }
    });

    if (!registered) {
        console.log('[PTT] Failed to register Alt+P shortcut');
    } else {
        console.log('[PTT] Alt+P registered as PTT hotkey');
    }

    // Note: For true PTT with keydown/keyup, consider using 'uiohook-napi' package
    // globalShortcut only fires on keydown, not keyup
}

// IPC handlers for renderer communication
ipcMain.on('ptt-activate', (event, channelKey: string) => {
    console.log(`PTT activated for channel: ${channelKey}`);
});

ipcMain.on('ptt-deactivate', (event, channelKey: string) => {
    console.log(`PTT deactivated for channel: ${channelKey}`);
});

// Setup persistent sessions for webviews
function setupWebviewSessions(): void {
    // WhatsApp session
    session.fromPartition('persist:whatsapp');

    // Teams session
    session.fromPartition('persist:teams');
}

app.whenReady().then(() => {
    setupWebviewSessions();
    createWindow();

    // [FIX] Bypass SSL certificate errors for self-signed PBX certs (dev only)
    app.on('certificate-error', (event, webContents, url, error, certificate, callback) => {
        console.log('[SSL] Certificate error for:', url, error);
        // Bypass certificate validation for PBX WSS connection
        if (url.includes('103.154.80.172') || url.includes('wss://')) {
            event.preventDefault();
            callback(true); // Trust this certificate
        } else {
            callback(false);
        }
    });

    app.on('activate', () => {
        if (BrowserWindow.getAllWindows().length === 0) {
            createWindow();
        }
    });
});

app.on('window-all-closed', () => {
    globalShortcut.unregisterAll();
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

app.on('will-quit', () => {
    globalShortcut.unregisterAll();
});
