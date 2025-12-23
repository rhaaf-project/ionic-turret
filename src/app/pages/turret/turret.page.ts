// TurretPage - SmartX UI Port to Angular/Ionic
import { Component, OnInit, OnDestroy, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonicModule } from '@ionic/angular';
import { SipService } from '../../services/sip.service';
import { AudioService } from '../../services/audio.service';

declare var bootstrap: any; // Bootstrap JS

export interface Channel {
    key: string;
    name: string;
    extension: string;
    type: 'sip' | 'whatsapp' | 'teams';
    position: number;
    isActive: boolean;      // SIP connected (green)
    isCalling: boolean;     // Currently calling/ringing (yellow)
    isPtt: boolean;
    isSelected: boolean;
    volumeIn: number;
    volumeOut: number;
    groups: string[];
}

export interface Group {
    id: string;
    name: string;
    isActive: boolean;
    channels: string[];
}

export interface Extension {
    number: string;
    online: boolean;
}

export interface Contact {
    id: string;
    name: string;
    phones: string[];  // Multiple phone numbers
    expanded?: boolean;
}

export interface Recording {
    id: string;
    name: string;
    duration?: string;
    date?: string;
}

export interface Tab {
    id: string;
    title: string;
    isClosable: boolean;
    isEditMode?: boolean;  // For FAV tabs view/edit toggle
    items: (DashboardIcon | null)[];  // 24-slot grid per tab
}

export interface DashboardIcon {
    id: string;
    label: string;
    icon: string;
    color: string;
    bgColor: string;
    panel?: string;
    isDeletable?: boolean;
}

@Component({
    selector: 'app-turret',
    standalone: true,
    imports: [CommonModule, IonicModule, FormsModule],
    schemas: [CUSTOM_ELEMENTS_SCHEMA],
    templateUrl: './turret.page.html',
    styleUrls: ['./turret.page.scss']
})
export class TurretPage implements OnInit, OnDestroy {
    // Header
    currentDate = '';
    currentTime = '';
    pttTargetName = 'Unknown Channel';

    // Incoming Call
    incomingCall: { number: string; channelKey: string } | null = null;

    // Channels (16)
    channels: Channel[] = [];
    selectedChannel: Channel | null = null;

    // Groups
    groups: Group[] = [
        { id: 'G1', name: 'Group 1', isActive: false, channels: [] },
        { id: 'G2', name: 'Group 2', isActive: false, channels: [] },
        { id: 'G3', name: 'Group 3', isActive: false, channels: [] }
    ];

    // Virtual Keyboard State
    vkActive = false;
    vkShift = false;
    vkSymbols = false;
    vkInput: HTMLInputElement | null = null;
    vkFirstKey = true;  // [FIX] Track if first key after VK opens (for select-all replace)

    // Login State
    showLogin = true;  // Show login on start
    loginUsername = '';
    loginPassword = '';
    loginStatus = 'System Ready';
    loginStatusColor = '#666';

    // PTT State
    pttActive = false;
    private pttJustFired = false;

    // Dialpad State
    dialpadNumber = '';
    dialpadContactName = '';
    dialpadConnected = false;

    // Duplicate Modal State
    showDuplicateModal = false;
    duplicateModalMessage = '';

    // === TABS SYSTEM ===
    tabs: Tab[] = [];
    activeTabId = 'dashboard';

    // Get current active tab
    get activeTab(): Tab | undefined {
        return this.tabs.find(t => t.id === this.activeTabId);
    }

    // Get current tab's items (for template binding)
    get dashboardLayout(): (DashboardIcon | null)[] {
        return this.activeTab?.items || [];
    }

    // Initialize tabs with default dashboard
    private initTabs(): void {
        this.tabs = [{
            id: 'dashboard',
            title: 'DASHBOARD',
            isClosable: false,
            items: this.createDefaultDashboardItems()
        }];
        this.activeTabId = 'dashboard';
    }

    private createDefaultDashboardItems(): (DashboardIcon | null)[] {
        const items: (DashboardIcon | null)[] = new Array(24).fill(null);
        items[0] = { id: 'whatsapp', label: 'WhatsApp', icon: 'chat', color: '#25d366', bgColor: 'rgba(37, 211, 102, 0.2)', panel: 'whatsapp', isDeletable: false };
        items[1] = { id: 'teams', label: 'Teams', icon: 'groups', color: '#6264a7', bgColor: 'rgba(98, 100, 167, 0.2)', panel: 'teams', isDeletable: false };
        items[2] = { id: 'audiorec', label: 'Audio Rec', icon: 'play_circle', color: '#888', bgColor: 'rgba(255, 255, 255, 0.1)', panel: 'recordings', isDeletable: false };
        return items;
    }

    switchTab(tabId: string): void {
        this.activeTabId = tabId;
        console.log('Switched to tab:', tabId);
    }

    addNewTab(): void {
        const newTabId = `fav-${Date.now()}`;
        const newTab: Tab = {
            id: newTabId,
            title: `FAV ${this.tabs.length}`,
            isClosable: true,
            isEditMode: true,  // Start in edit mode like SmartX
            items: new Array(24).fill(null)
        };
        this.tabs.push(newTab);
        this.activeTabId = newTabId;
        this.favRenameInput = newTab.title;  // Set rename input value
        console.log('🆕 Added new tab:', newTab.title, 'activeTabId:', this.activeTabId);
        console.log('📋 All tabs:', this.tabs.map(t => t.id));
    }

    closeTab(tabId: string, event: Event): void {
        event.stopPropagation();
        const tab = this.tabs.find(t => t.id === tabId);
        if (!tab || !tab.isClosable) return;

        const index = this.tabs.findIndex(t => t.id === tabId);
        this.tabs.splice(index, 1);

        // Switch to previous tab or dashboard
        if (this.activeTabId === tabId) {
            this.activeTabId = this.tabs[Math.max(0, index - 1)]?.id || 'dashboard';
        }
        console.log('Closed tab:', tabId);
    }

    // FAV Tab rename input value (for edit mode)
    favRenameInput = '';

    // Enable edit mode for FAV tab
    enableFavEditMode(tabId: string): void {
        const tab = this.tabs.find(t => t.id === tabId);
        if (tab && tab.isClosable) {
            tab.isEditMode = true;
            this.favRenameInput = tab.title;
            console.log('Edit mode enabled for:', tab.title);
        }
    }

    // Save FAV tab rename AND create dashboard icon
    saveFavEdit(tabId: string): void {
        const tab = this.tabs.find(t => t.id === tabId);
        if (tab && this.favRenameInput.trim()) {
            tab.title = this.favRenameInput.trim();
            tab.isEditMode = false;

            // 1. Save to savedFavourites in localStorage
            const savedFavourites = JSON.parse(localStorage.getItem('smartucx_saved_favourites') || '{}');
            savedFavourites[tabId] = {
                title: tab.title,
                items: tab.items.filter(i => i !== null)
            };
            localStorage.setItem('smartucx_saved_favourites', JSON.stringify(savedFavourites));

            // 2. Add OR update fav icon in dashboard layout
            const dashboardTab = this.tabs.find(t => t.id === 'dashboard');
            if (dashboardTab) {
                const favIconId = `fav_${tabId}`;
                const existingIconIndex = dashboardTab.items.findIndex(item => item?.id === favIconId);

                if (existingIconIndex !== -1) {
                    // Update existing icon label
                    dashboardTab.items[existingIconIndex]!.label = tab.title;
                    console.log(`⭐ Updated ${tab.title} icon in dashboard slot ${existingIconIndex}`);
                } else {
                    // Add new icon to empty slot
                    const emptySlotIndex = dashboardTab.items.findIndex(item => item === null);
                    if (emptySlotIndex !== -1) {
                        dashboardTab.items[emptySlotIndex] = {
                            id: favIconId,
                            label: tab.title,
                            icon: 'star',
                            color: 'gold',
                            bgColor: '#333',
                            panel: `favourite_${tabId}`,
                            isDeletable: true
                        };
                        console.log(`⭐ Added ${tab.title} icon to dashboard slot ${emptySlotIndex}`);
                    }
                }
            }

            // 3. Hide VK
            this.hideVirtualKeyboard();

            console.log('✅ Saved favourite:', tab.title);
        }
    }

    // Cancel edit mode
    cancelFavEdit(tabId: string): void {
        const tab = this.tabs.find(t => t.id === tabId);
        if (tab) {
            tab.isEditMode = false;
            this.hideVirtualKeyboard();
            console.log('Cancelled edit for:', tab.title);
        }
    }

    // Focus rename input and show VK
    focusRenameInput(): void {
        setTimeout(() => {
            const input = document.querySelector('.fav-rename-input') as HTMLInputElement;
            if (input) {
                input.focus();
                this.showVirtualKeyboard();
                console.log('📝 Rename input focused, VK shown');
            }
        }, 100);
    }

    // Show virtual keyboard (uses vkActive Angular property)
    showVirtualKeyboard(): void {
        this.vkActive = true;
        console.log('⌨️ VK shown');
    }

    // Hide virtual keyboard
    hideVirtualKeyboard(): void {
        this.vkActive = false;
        console.log('⌨️ VK hidden');
    }


    // Legacy renameTab (prompt-based, for double-click in tab bar)
    renameTab(tabId: string): void {
        const tab = this.tabs.find(t => t.id === tabId);
        if (tab && tab.isClosable) {
            const newName = prompt('Enter new tab name:', tab.title);
            if (newName) {
                tab.title = newName;
            }
        }
    }

    // Drag & Drop State
    draggedChannel: Channel | null = null;
    dragOverChannel: string | null = null;
    draggedIcon: any = null;
    dragOverEmptyCell: number | null = null;
    dragOverTrash = false;
    dragOverActionBtn: string | null = null;

    // Footer
    systemStatus = 'READY';
    connectionStatus = 'LOW LATENCY';
    traderId = '4759';

    // Drawer: Submenu state
    submenuOpen: string | null = null;

    // Drawer: Lines, VPW, CAS (matching SmartX drawer_data structure)
    lines: { id: string; name: string; use_ext: string }[] = [
        { id: '9', name: 'BCA FX', use_ext: '1001' },
        { id: '10', name: 'BI FX', use_ext: '1002' },
        { id: '11', name: 'BNI FI', use_ext: '1003' },
        { id: '12', name: 'Intl', use_ext: '1004' }
    ];
    vpwList: { id: string; name: string; use_ext: string }[] = [
        { id: '13', name: 'JPMC FX', use_ext: '2001' },
        { id: '14', name: 'DBS MM', use_ext: '2002' },
        { id: '15', name: 'VPW HQ Jakarta', use_ext: '2003' }
    ];
    casList: { id: string; name: string; use_ext: string }[] = [
        { id: '16', name: 'CITI FX', use_ext: '3001' }
    ];

    // Drawer: Extensions
    extensions: Extension[] = [
        { number: '6001', online: true },
        { number: '6002', online: true },
        { number: '6003', online: false },
        { number: '6004', online: true },
        { number: '6005', online: false }
    ];

    // Drawer: Intercom
    intercomContacts: { name: string; ext: string }[] = [
        { name: 'John Trader', ext: '3001' },
        { name: 'Sarah FX Desk', ext: '3002' },
        { name: 'Mike Crypto', ext: '3003' },
        { name: 'Emily Bond', ext: '3004' },
        { name: 'David Metal', ext: '3005' }
    ];

    // Drawer: Phonebook
    phoneSearchQuery = '';
    alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
    contacts: Contact[] = [];
    filteredContacts: Contact[] = [];

    // Drawer: Recordings
    audioRecordings: Recording[] = [
        { id: '1', name: 'Meeting Notes', duration: '02:30' },
        { id: '2', name: 'Client Call', duration: '05:15' }
    ];
    callRecordings: Recording[] = [
        { id: '1', name: 'Call with VPW', date: '2025-12-18' }
    ];

    // Timer
    private clockInterval: any;

    constructor(
        private sipService: SipService,
        private audioService: AudioService
    ) { }

    ngOnInit(): void {
        this.initChannels();
        this.initContacts();
        this.initTabs();
        this.startClock();
        this.audioService.resumeContext();
        this.setupPttListener();
        this.setupVkTouchListener();
        this.setupSipSubscriptions();  // [FIX] Subscribe to SIP events
    }

    // [FIX] Subscribe to SIP service events for UI updates
    private activeChannelKeys = new Set<string>();  // Track which channels have active calls

    private setupSipSubscriptions(): void {
        // Watch for session state changes
        this.sipService.activeSessions$.subscribe(sessions => {
            // Check for new connected sessions
            sessions.forEach((sipSession, channelKey) => {
                const index = this.channels.findIndex(c => c.key === channelKey);
                if (index !== -1) {
                    if (sipSession.state === 'connected') {
                        // Call connected - update to active (green)
                        this.channels[index] = {
                            ...this.channels[index],
                            isCalling: false,
                            isActive: true
                        };
                        this.activeChannelKeys.add(channelKey);
                        console.log('✅ SIP Connected:', channelKey);
                    }
                }
            });

            // Check for ended sessions (channel was active but no longer in sessions)
            this.activeChannelKeys.forEach(channelKey => {
                if (!sessions.has(channelKey)) {
                    const index = this.channels.findIndex(c => c.key === channelKey);
                    if (index !== -1) {
                        // Call ended - reset channel state
                        this.channels[index] = {
                            ...this.channels[index],
                            isCalling: false,
                            isActive: false
                        };
                        console.log('📴 SIP Session ended:', channelKey);
                    }
                    this.activeChannelKeys.delete(channelKey);
                }
            });

            this.channels = [...this.channels];
        });

        // Watch for registration status
        this.sipService.registrationStatus$.subscribe(status => {
            console.log('📞 SIP Registration status:', status);
            if (status === 'registered') {
                this.connectionStatus = 'CONNECTED';
            } else if (status === 'failed') {
                this.connectionStatus = 'DISCONNECTED';
            }
        });

        // Watch for incoming calls
        this.sipService.incomingCall$.subscribe(call => {
            if (call) {
                console.log('📲 Incoming call:', call.number);
                this.incomingCall = call;
            } else {
                this.incomingCall = null;
            }
        });
    }

    // Setup VK touch listener (like SmartX)
    private setupVkTouchListener(): void {
        document.addEventListener('touchend', (e: TouchEvent) => {
            const vkEl = document.getElementById('virtualKeyboard');
            if (vkEl && vkEl.contains(e.target as Node)) {
                let target = e.target as HTMLElement;

                // Handle nested elements - find kb-key or kb-close-icon
                if (!target.classList.contains('kb-key') && !target.classList.contains('kb-close-icon')) {
                    target = target.closest('.kb-key') as HTMLElement ||
                        target.closest('.kb-close-icon') as HTMLElement;
                }

                if (target) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Dispatch proper MouseEvent for Angular compatibility
                    const clickEvent = new MouseEvent('click', {
                        bubbles: true,
                        cancelable: true,
                        view: window
                    });
                    target.dispatchEvent(clickEvent);
                    console.log('⌨️ VK touch dispatched click on:', target.textContent?.trim());
                }
            }
        }, { capture: true, passive: false });

        console.log('⌨️ VK touch listener setup');
    }

    // Setup PTT hotkey listener from Electron
    private setupPttListener(): void {
        // Check if running in Electron
        if ((window as any).electronAPI) {
            (window as any).electronAPI.onPttKeyDown((key: string) => {
                console.log('[PTT] Hotkey received:', key);
                this.togglePtt();
            });
            console.log('[PTT] Electron IPC listener registered');
        } else {
            console.log('[PTT] Not running in Electron, PTT hotkey disabled');
        }
    }

    // Toggle PTT state (for global hotkey)
    togglePtt(): void {
        this.pttActive = !this.pttActive;
        console.log('[PTT] Active:', this.pttActive);

        // Update target name based on selected channel
        if (this.pttActive && this.selectedChannel) {
            this.pttTargetName = this.selectedChannel.name || 'Unknown Channel';
        }
    }

    // Start PTT on mousedown (like SmartX)
    startPTT(event: MouseEvent, channel: Channel): void {
        // [FIX] Skip PTT if dragging from drag-handle
        if ((event.target as HTMLElement).closest('.drag-handle')) {
            return;
        }

        // Only allow PTT on active channels - for inactive, let click handle selection
        if (!channel.isActive) return;

        // Prevent click from triggering selection ONLY for PTT
        event.preventDefault();
        event.stopPropagation();

        // Mark that PTT was used (prevents selection toggle)
        this.pttJustFired = true;

        console.log('🎤 PTT ON:', channel.key);
        channel.isPtt = true;
        this.pttActive = true;
        this.pttTargetName = channel.name || 'Unknown Channel';
    }

    // Stop PTT on mouseup/mouseleave (like SmartX)
    stopPTT(channel: Channel): void {
        if (!channel.isPtt) return;

        console.log('🔇 PTT OFF:', channel.key);
        channel.isPtt = false;
        this.pttActive = false;
    }

    ngOnDestroy(): void {
        if (this.clockInterval) clearInterval(this.clockInterval);
    }

    private initChannels(): void {
        // Empty channels - no name means unassigned
        for (let i = 0; i < 16; i++) {
            this.channels.push({
                key: `ch-${i + 1}`,
                name: `Ch ${i + 1}`,   // Show Ch 1 - Ch 16
                extension: '',
                type: 'sip',
                position: i + 1,
                isActive: false,    // Not connected
                isCalling: false,   // Not calling
                isPtt: false,
                isSelected: false,
                volumeIn: 0,
                volumeOut: 0,
                groups: []
            });
        }
    }

    private initContacts(): void {
        this.contacts = [
            { id: '1', name: 'Alice Smith', phones: ['021-1234567', '0812-1234567'] },
            { id: '2', name: 'Bob Johnson', phones: ['021-2345678', '0813-2345678'] },
            { id: '3', name: 'Charlie Brown', phones: ['021-3456789'] },
            { id: '4', name: 'David Chen', phones: ['021-4567890', '0814-4567890', '021-4567891'] },
            { id: '5', name: 'Emily Davis', phones: ['021-5678901'] },
            { id: '6', name: 'Frank Wilson', phones: ['021-6789012', '0815-6789012'] },
            { id: '7', name: 'Grace Lee', phones: ['021-7890123'] },
            { id: '8', name: 'Henry Martinez', phones: ['021-8901234', '0816-8901234'] },
            { id: '9', name: 'Ivy Thompson', phones: ['021-9012345'] },
            { id: '10', name: 'Jack Anderson', phones: ['021-0123456', '0817-0123456'] },
            { id: '11', name: 'Karen White', phones: ['021-1112233'] },
            { id: '12', name: 'Larry Green', phones: ['021-2223344', '0818-2223344'] },
            { id: '13', name: 'Mary Taylor', phones: ['021-3334455'] },
            { id: '14', name: 'Nick Brown', phones: ['021-4445566', '0819-4445566'] },
            { id: '15', name: 'Olivia Jones', phones: ['021-5556677'] },
            { id: '16', name: 'Peter King', phones: ['021-6667788', '0821-6667788'] },
            { id: '17', name: 'Rachel Moore', phones: ['021-7778899'] },
            { id: '18', name: 'Steve Harris', phones: ['021-8889900', '0822-8889900'] },
            { id: '19', name: 'Tina Clark', phones: ['021-9990011'] },
            { id: '20', name: 'Victor Lewis', phones: ['021-0001122', '0823-0001122'] },
            { id: '21', name: 'Wendy Hall', phones: ['021-1122334'] },
            { id: '22', name: 'Xavier Young', phones: ['021-2233445', '0824-2233445'] },
            { id: '23', name: 'Yvonne Allen', phones: ['021-3344556'] },
            { id: '24', name: 'Zack Robinson', phones: ['021-4455667', '0825-4455667'] }
        ];
        this.filteredContacts = [...this.contacts];
    }

    private startClock(): void {
        this.updateClock();
        this.clockInterval = setInterval(() => this.updateClock(), 1000);
    }

    private updateClock(): void {
        const now = new Date();
        const months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        this.currentDate = `${months[now.getMonth()]} ${now.getDate()} ${now.getFullYear()}`;
        this.currentTime = now.toLocaleTimeString('en-US', { hour12: true });
    }

    // === CHANNEL ACTIONS ===
    selectChannel(channel: Channel): void {
        // Skip selection if PTT was just used (prevents toggle on mouseup->click)
        if (this.pttJustFired) {
            this.pttJustFired = false;
            return;
        }
        this.channels.forEach(ch => ch.isSelected = false);
        channel.isSelected = true;
        this.selectedChannel = channel;
    }

    callChannel(): void {
        console.log('📞 callChannel called, selectedChannel:', this.selectedChannel?.key, 'name:', this.selectedChannel?.name, 'ext:', this.selectedChannel?.extension);

        if (this.selectedChannel && this.selectedChannel.name) {
            // Must have assigned contact to call
            if (!this.selectedChannel.extension) {
                console.log('⚠️ No phone number assigned to channel');
                return;
            }

            // Set calling state (yellow)
            const index = this.channels.findIndex(c => c.key === this.selectedChannel!.key);
            console.log('📞 Found channel at index:', index);

            if (index !== -1) {
                this.channels[index] = {
                    ...this.channels[index],
                    isCalling: true,
                    isSelected: true
                };
                this.channels = [...this.channels];
                this.selectedChannel = this.channels[index];
                console.log('📞 Calling:', this.selectedChannel.key, this.selectedChannel.extension, 'isCalling:', this.channels[index].isCalling);

                // [FIX] Make actual SIP call
                const targetUri = `sip:${this.selectedChannel.extension}@103.154.80.172`;
                this.sipService.makeCall(targetUri, this.selectedChannel.key);
            }
        } else {
            console.log('⚠️ No channel selected or channel has no name');
        }
    }


    hangupSelected(): void {
        if (this.selectedChannel) {
            const index = this.channels.findIndex(c => c.key === this.selectedChannel!.key);
            if (index !== -1) {
                // Reset all call states
                this.channels[index] = {
                    ...this.channels[index],
                    isActive: false,
                    isCalling: false,
                    isPtt: false
                };
                this.channels = [...this.channels];
                console.log('📴 Hangup channel:', this.selectedChannel.key);
            }
        }
    }

    // === GROUP PTT ===
    startGroupPtt(group: Group): void {
        group.isActive = true;
        this.audioService.startPtt();
    }

    stopGroupPtt(group: Group): void {
        group.isActive = false;
        this.audioService.stopPtt();
    }

    // === INCOMING CALL ===
    acceptCall(): void {
        if (this.incomingCall) {
            this.incomingCall = null;
        }
    }

    rejectCall(): void {
        if (this.incomingCall) {
            this.incomingCall = null;
        }
    }

    // === DASHBOARD ===
    openPanel(panel: string): void {
        console.log('Open panel:', panel);

        // Handle favourite panels - format: favourite_fav-xxx
        if (panel.startsWith('favourite_')) {
            const tabId = panel.replace('favourite_', '');

            // Check if tab already exists in tabs array
            const existingTab = this.tabs.find(t => t.id === tabId);
            if (existingTab) {
                // Just switch to it
                this.activeTabId = tabId;
                console.log('⭐ Switched to existing favourite tab:', tabId);
            } else {
                // Load from savedFavourites and create tab
                const savedFavourites = JSON.parse(localStorage.getItem('smartucx_saved_favourites') || '{}');
                const favData = savedFavourites[tabId];

                if (favData) {
                    // Create tab from saved data
                    const newTab: Tab = {
                        id: tabId,
                        title: favData.title || 'Favourite',
                        isClosable: true,
                        isEditMode: false,
                        items: favData.items || new Array(24).fill(null)
                    };
                    this.tabs.push(newTab);
                    this.activeTabId = tabId;
                    console.log('⭐ Loaded favourite tab from storage:', tabId);
                } else {
                    console.warn('Favourite not found in storage:', tabId);
                }
            }
        }
    }

    addItem(): void {
        // Find first empty slot
        const emptySlot = this.dashboardLayout.findIndex(slot => slot === null);
        if (emptySlot === -1) {
            console.log('No empty slots available');
            return;
        }

        // Create a new Favourite icon
        const favCount = this.dashboardLayout.filter(i => i?.id.startsWith('fav-')).length + 1;
        const newFav = {
            id: `fav-${Date.now()}`,
            label: `Favourite ${favCount}`,
            icon: 'star',
            color: '#ff4d4d',
            bgColor: 'rgba(255, 77, 77, 0.1)',
            panel: 'favourite',
            isDeletable: true
        };

        // Place in first empty slot
        if (this.activeTab) {
            this.activeTab.items[emptySlot] = newFav;
            this.tabs = [...this.tabs];  // Trigger change detection
        }
        console.log('Added new favourite:', newFav.id, 'at slot', emptySlot);
    }

    deleteItem(): void { console.log('Delete item'); }

    // Get count of items in a favourite tab (for dashboard badge)
    getFavItemCount(iconId: string): number {
        // iconId is like 'fav_xxx', we need to look up the tab or savedFavourites
        const tabId = iconId.replace('fav_', '');

        // Check in current tabs first
        const tab = this.tabs.find(t => t.id === tabId);
        if (tab && tab.items) {
            return tab.items.filter((item: any) => item !== null).length;
        }

        // Otherwise check in localStorage
        const savedFavourites = JSON.parse(localStorage.getItem('smartucx_saved_favourites') || '{}');
        const favData = savedFavourites[tabId];
        if (favData && favData.items) {
            return favData.items.filter((item: any) => item !== null).length;
        }

        return 0;
    }

    // === HEADER ===
    openSettings(): void {
        const drawer = document.getElementById('toolsDrawer');
        if (drawer && bootstrap?.Offcanvas) {
            new bootstrap.Offcanvas(drawer).show();
        }
    }

    openDialpad(): void {
        const drawer = document.getElementById('dialpadDrawer');
        if (drawer && bootstrap?.Offcanvas) {
            new bootstrap.Offcanvas(drawer).show();
        }
    }

    // === DRAWER: SETTINGS ===
    toggleSubmenu(menu: string): void {
        this.submenuOpen = this.submenuOpen === menu ? null : menu;
    }

    openTab(tab: string): void {
        console.log('Open tab:', tab);
    }

    signOut(): void {
        console.log('Sign out');
    }

    // === DRAWER: INTERCOM ===
    callIntercom(contact: { name: string; ext: string }): void {
        console.log('Call intercom:', contact.name);
    }

    // === DRAWER: PHONEBOOK ===
    filterPhonebook(): void {
        const q = this.phoneSearchQuery.toLowerCase();
        if (!q) {
            this.filteredContacts = [...this.contacts];
            return;
        }
        this.filteredContacts = this.contacts.filter(c =>
            c.name.toLowerCase().includes(q) ||
            c.phones.some(p => p.includes(q))
        );
    }

    scrollToLetter(letter: string): void {
        // Find first contact starting with this letter
        const contact = this.filteredContacts.find(c =>
            c.name.toUpperCase().startsWith(letter)
        );
        if (contact) {
            const contactList = document.getElementById('contact-list-scroll');
            const contactItems = contactList?.querySelectorAll('.contact-item');
            const index = this.filteredContacts.indexOf(contact);
            if (contactItems && contactItems[index]) {
                contactItems[index].scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }

    callContact(contact: Contact): void {
        console.log('Call contact:', contact.name, contact.phones[0]);
    }

    toggleContactExpand(contact: Contact): void {
        contact.expanded = !contact.expanded;
    }

    dialPhone(phone: string): void {
        console.log('📞 Dialing phone:', phone);
        // Set dialpad number and open dialpad
        this.dialpadNumber = phone;
        const drawer = document.getElementById('dialpadDrawer');
        if (drawer && bootstrap?.Offcanvas) {
            new bootstrap.Offcanvas(drawer).show();
        }
    }

    handlePhoneDragStart(event: DragEvent, phone: string, contactName?: string): void {
        if (event.dataTransfer) {
            // Legacy format for channel drops
            event.dataTransfer.setData('text/plain', phone);
            event.dataTransfer.setData('dialpad-number', phone);
            if (contactName) {
                event.dataTransfer.setData('contact-name', contactName);
            }

            // JSON format for favourite drops (phonebook type)
            const dragData = JSON.stringify({
                type: 'phonebook',
                id: `contact_${Date.now()}`,
                name: contactName || 'Unknown',
                phone: phone
            });
            event.dataTransfer.setData('application/json', dragData);

            event.dataTransfer.effectAllowed = 'copy';
            console.log('📞 Dragging phone:', phone, 'Contact:', contactName);
        }
    }

    // Touch drag state
    private touchDragData: { phone: string; contactName: string; startX: number; startY: number; isDragging: boolean; clone?: HTMLElement } | null = null;

    handlePhoneTouchStart(event: TouchEvent, phone: string, contactName: string): void {
        const touch = event.touches[0];
        this.touchDragData = {
            phone,
            contactName,
            startX: touch.clientX,
            startY: touch.clientY,
            isDragging: false
        };
        console.log('Touch start:', phone, contactName);
    }

    handlePhoneTouchMove(event: TouchEvent): void {
        if (!this.touchDragData) return;

        const touch = event.touches[0];
        const deltaX = Math.abs(touch.clientX - this.touchDragData.startX);
        const deltaY = Math.abs(touch.clientY - this.touchDragData.startY);

        // Start dragging if moved beyond threshold
        if (!this.touchDragData.isDragging && (deltaX > 10 || deltaY > 10)) {
            this.touchDragData.isDragging = true;
            event.preventDefault();

            // Create visual drag clone
            const clone = document.createElement('div');
            clone.className = 'touch-drag-clone';
            clone.textContent = this.touchDragData.contactName;
            clone.style.cssText = `
                position: fixed;
                z-index: 9999;
                background: var(--color-accent-green);
                color: #000;
                padding: 8px 16px;
                border-radius: 8px;
                font-weight: bold;
                pointer-events: none;
                transform: translate(-50%, -50%);
                left: ${touch.clientX}px;
                top: ${touch.clientY}px;
            `;
            document.body.appendChild(clone);
            this.touchDragData.clone = clone;
            console.log('Touch drag started:', this.touchDragData.contactName);
        }

        if (this.touchDragData.isDragging && this.touchDragData.clone) {
            event.preventDefault();
            // Move clone with touch
            this.touchDragData.clone.style.left = `${touch.clientX}px`;
            this.touchDragData.clone.style.top = `${touch.clientY}px`;

            // Highlight potential drop targets (channels AND favourite slots)
            const elements = document.elementsFromPoint(touch.clientX, touch.clientY);
            const channelCell = elements.find(el => el.classList.contains('call-cell'));
            const favSlot = elements.find(el => el.classList.contains('favourite-item') && el.classList.contains('empty-slot'));

            document.querySelectorAll('.call-cell, .favourite-item.empty-slot').forEach(cell => cell.classList.remove('drag-over'));
            if (channelCell) {
                channelCell.classList.add('drag-over');
            }
            if (favSlot) {
                favSlot.classList.add('drag-over');
            }
        }
    }

    handlePhoneTouchEnd(event: TouchEvent): void {
        if (!this.touchDragData) return;

        // Remove clone
        if (this.touchDragData.clone) {
            this.touchDragData.clone.remove();
        }

        if (this.touchDragData.isDragging) {
            // Find drop target
            const touch = event.changedTouches[0];
            const elements = document.elementsFromPoint(touch.clientX, touch.clientY);
            const channelCell = elements.find(el => el.classList.contains('call-cell')) as HTMLElement;
            const favSlot = elements.find(el => el.classList.contains('favourite-item') && el.classList.contains('empty-slot')) as HTMLElement;

            // Handle drop on channel
            if (channelCell) {
                const channelKey = channelCell.dataset['channelKey'];
                const targetChannel = this.channels.find(c => c.key === channelKey);

                if (targetChannel) {
                    // Check for duplicate
                    const existingChannel = this.channels.find(c => c.extension === this.touchDragData!.phone);
                    if (existingChannel) {
                        console.log(`⚠️ Number already in ${existingChannel.key}`);
                    } else {
                        // Assign to channel (not active yet - need to CALL)
                        console.log(`📞 Touch drop ${this.touchDragData.contactName} to ${channelKey}`);
                        targetChannel.extension = this.touchDragData.phone;
                        targetChannel.name = this.touchDragData.contactName;
                        // Don't set isActive - need to call first

                        // [FIX] Auto-select channel after drop
                        this.channels.forEach(ch => ch.isSelected = false);
                        targetChannel.isSelected = true;
                        this.selectedChannel = targetChannel;

                        this.channels = [...this.channels];

                        // Close phonebook drawer
                        const drawer = document.getElementById('phonebookDrawer');
                        if (drawer && bootstrap?.Offcanvas) {
                            bootstrap.Offcanvas.getInstance(drawer)?.hide();
                        }
                    }
                }
            }
            // Handle drop on favourite slot
            else if (favSlot && this.activeTab && this.activeTabId !== 'dashboard') {
                const slotIndex = parseInt(favSlot.dataset['index'] || '-1');
                if (slotIndex >= 0 && slotIndex < 24) {
                    const newItem = {
                        id: `contact_${Date.now()}`,
                        label: this.touchDragData.contactName,
                        icon: 'contact_page',
                        color: 'var(--color-accent-green)',
                        bgColor: '#333',
                        isDeletable: true
                    };

                    // Check for duplicates
                    const isDuplicate = this.activeTab.items.some((item: any) => item?.label === newItem.label);
                    if (!isDuplicate) {
                        this.activeTab.items[slotIndex] = newItem;
                        this.tabs = [...this.tabs];
                        console.log(`⭐ Touch drop ${this.touchDragData.contactName} to favourite slot ${slotIndex}`);

                        // Close phonebook drawer
                        const drawer = document.getElementById('phonebookDrawer');
                        if (drawer && bootstrap?.Offcanvas) {
                            bootstrap.Offcanvas.getInstance(drawer)?.hide();
                        }
                    } else {
                        console.log('⚠️ Contact already in favourite');
                    }
                }
            }

            // Clear drag-over styling
            document.querySelectorAll('.call-cell, .favourite-item.empty-slot').forEach(cell => cell.classList.remove('drag-over'));
        }

        this.touchDragData = null;
    }

    // === DRAWER: DIALPAD ===
    dialNum(num: string): void {
        this.dialpadNumber += num;
    }

    clearDialpad(): void {
        this.dialpadNumber = '';
        this.dialpadContactName = '';
    }

    dialFromPad(): void {
        if (this.dialpadNumber) {
            console.log('Dialing:', this.dialpadNumber);
            this.dialpadConnected = true;
        }
    }

    hangupDialpad(): void {
        this.dialpadConnected = false;
        this.dialpadNumber = '';
    }

    startDialpadPTT(): void {
        this.audioService.startPtt();
    }

    stopDialpadPTT(): void {
        this.audioService.stopPtt();
    }

    // Handle drag from dialpad display to channel cells
    handleDialDragStart(event: DragEvent): void {
        if (this.dialpadNumber && event.dataTransfer) {
            event.dataTransfer.setData('text/plain', this.dialpadNumber);
            event.dataTransfer.setData('dialpad-number', this.dialpadNumber);
            event.dataTransfer.effectAllowed = 'copy';
            console.log('Dragging dialpad number:', this.dialpadNumber);
        }
    }

    // [NEW] Open dialpad with pre-filled number (double-click on Line item)
    openDialpadWithNumber(number: string, name: string): void {
        console.log('📞 Opening dialpad with:', number, name);
        this.dialpadNumber = number;
        this.dialpadContactName = name;

        // Close line drawer first
        const lineDrawer = document.getElementById('lineListDrawer');
        if (lineDrawer && bootstrap?.Offcanvas) {
            bootstrap.Offcanvas.getInstance(lineDrawer)?.hide();
        }

        // Open dialpad drawer
        setTimeout(() => {
            const dialpadDrawer = document.getElementById('dialpadDrawer');
            if (dialpadDrawer && bootstrap?.Offcanvas) {
                const offcanvas = new bootstrap.Offcanvas(dialpadDrawer);
                offcanvas.show();
            }
        }, 200);
    }

    // === DRAWER: RECORDINGS ===
    showRecordingPanel(): void {
        console.log('Show recording panel');
    }

    onRecordingDragStart(event: DragEvent, rec: Recording): void {
        event.dataTransfer?.setData('recording', JSON.stringify(rec));
    }

    // ============================================
    // DRAG & DROP: CHANNELS
    // ============================================
    onChannelDragStart(event: DragEvent, channel: Channel): void {
        this.draggedChannel = channel;
        event.dataTransfer?.setData('channel', JSON.stringify({ key: channel.key, type: 'channel_move' }));
        if (event.dataTransfer) event.dataTransfer.effectAllowed = 'move';
        console.log('Drag start:', channel.key);
    }

    onChannelDragOver(event: DragEvent, channel: Channel): void {
        event.preventDefault();
        if (this.draggedChannel && this.draggedChannel.key !== channel.key) {
            this.dragOverChannel = channel.key;
        }
    }

    onChannelDragLeave(event: DragEvent): void {
        this.dragOverChannel = null;
    }

    onChannelDrop(event: DragEvent, targetChannel: Channel): void {
        event.preventDefault();
        this.dragOverChannel = null;

        // Check if this is a dialpad number drop
        if (event.dataTransfer) {
            const dialpadNumber = event.dataTransfer.getData('dialpad-number');
            if (dialpadNumber) {
                // Check if this number is already assigned to another channel
                const existingChannel = this.channels.find(c => c.extension === dialpadNumber);
                if (existingChannel) {
                    console.log(`⚠️ Number ${dialpadNumber} already in ${existingChannel.key} - skipping`);
                    return; // Don't allow duplicate
                }

                // Get contact name if available (from phonebook drag)
                const contactName = event.dataTransfer.getData('contact-name');

                // Assign to this channel
                console.log(`📞 Dropping ${contactName || dialpadNumber} to channel ${targetChannel.key}`);
                targetChannel.extension = dialpadNumber;
                targetChannel.name = contactName || dialpadNumber;  // Use contact name if available
                // Don't set isActive - need to call first

                // Auto-select channel after drop
                this.channels.forEach(ch => ch.isSelected = false);
                targetChannel.isSelected = true;
                this.selectedChannel = targetChannel;

                // Force update
                this.channels = [...this.channels];

                // Clear dialpad display
                this.dialpadNumber = '';

                // Close drawers
                const dialpadDrawer = document.getElementById('dialpadDrawer');
                const phonebookDrawer = document.getElementById('phonebookDrawer');
                if (dialpadDrawer && bootstrap?.Offcanvas) {
                    bootstrap.Offcanvas.getInstance(dialpadDrawer)?.hide();
                }
                if (phonebookDrawer && bootstrap?.Offcanvas) {
                    bootstrap.Offcanvas.getInstance(phonebookDrawer)?.hide();
                }
                return;
            }

            // [FIX] Handle Line/VPW/CAS mouse drag drop
            const jsonData = event.dataTransfer.getData('application/json');
            if (jsonData) {
                try {
                    const data = JSON.parse(jsonData);
                    if (data.type === 'line_vpw_cas_item') {
                        console.log(`📞 Mouse drop ${data.name} (${data.itemType}) to channel ${targetChannel.key}`);

                        // Check for duplicate
                        const existingChannel = this.channels.find(c => c.extension === data.itemId);
                        if (existingChannel) {
                            console.log(`⚠️ Extension ${data.itemId} already in ${existingChannel.key}`);
                            return;
                        }

                        targetChannel.name = data.name;
                        targetChannel.extension = data.itemId;
                        // Don't set isActive - need to call first

                        // Auto-select channel after drop
                        this.channels.forEach(ch => ch.isSelected = false);
                        targetChannel.isSelected = true;
                        this.selectedChannel = targetChannel;

                        this.channels = [...this.channels];

                        // Close drawer
                        const lineDrawer = document.getElementById('lineListDrawer');
                        const vpwDrawer = document.getElementById('vpwListDrawer');
                        const casDrawer = document.getElementById('casListDrawer');
                        [lineDrawer, vpwDrawer, casDrawer].forEach(drawer => {
                            if (drawer && bootstrap?.Offcanvas) {
                                bootstrap.Offcanvas.getInstance(drawer)?.hide();
                            }
                        });
                        return;
                    }

                    // Handle favourite item drop to channel (non-edit mode)
                    if (data.type === 'favourite_to_channel') {
                        console.log(`📞 Favourite item drop: ${data.name} to channel ${targetChannel.key}`);

                        // Check for duplicate
                        const existingChannel = this.channels.find(c => c.name === data.name || c.extension === data.phone);
                        if (existingChannel && existingChannel.key !== targetChannel.key) {
                            console.log(`⚠️ Item "${data.name}" already in ${existingChannel.key}`);
                            this.duplicateModalMessage = `"${data.name}" already listed on Channel`;
                            this.showDuplicateModal = true;
                            return;
                        }

                        targetChannel.name = data.name;
                        targetChannel.extension = data.phone || '';

                        // Auto-select channel after drop
                        this.channels.forEach(ch => ch.isSelected = false);
                        targetChannel.isSelected = true;
                        this.selectedChannel = targetChannel;

                        this.channels = [...this.channels];
                        console.log(`✅ Copied ${data.name} to channel ${targetChannel.key}`);
                        return;
                    }
                } catch (e) {
                    console.log('Could not parse drag data');
                }
            }
        }

        if (this.draggedChannel && this.draggedChannel.key !== targetChannel.key) {
            // Swap channels
            const sourceIndex = this.channels.findIndex(c => c.key === this.draggedChannel!.key);
            const targetIndex = this.channels.findIndex(c => c.key === targetChannel.key);

            if (sourceIndex !== -1 && targetIndex !== -1) {
                // Swap data
                const temp = { ...this.channels[sourceIndex] };
                this.channels[sourceIndex] = { ...this.channels[targetIndex], key: this.channels[sourceIndex].key, position: sourceIndex + 1 };
                this.channels[targetIndex] = { ...temp, key: this.channels[targetIndex].key, position: targetIndex + 1 };
                console.log(`Swapped ${this.draggedChannel.key} with ${targetChannel.key}`);
            }
        }
        this.draggedChannel = null;
    }

    // ============================================
    // DRAG HANDLE: Channel Drag to Hangup/Delete
    // ============================================
    dragHandleChannel: Channel | null = null;
    dragHandleClone: HTMLElement | null = null;

    onDragHandleStart(event: DragEvent, channel: Channel): void {
        this.dragHandleChannel = channel;
        if (event.dataTransfer) {
            event.dataTransfer.setData('channel-key', channel.key);
            event.dataTransfer.effectAllowed = 'move';
        }
        console.log('🔧 Drag handle start:', channel.key);
    }

    onDragHandleTouchStart(event: TouchEvent, channel: Channel): void {
        event.preventDefault();
        event.stopPropagation();

        this.dragHandleChannel = channel;

        // Create visual clone
        const touch = event.touches[0];
        const clone = document.createElement('div');
        clone.className = 'drag-clone';
        clone.innerHTML = `<span class="material-symbols-sharp">drag_indicator</span> ${channel.name}`;
        clone.style.cssText = `
            position: fixed;
            left: ${touch.clientX - 30}px;
            top: ${touch.clientY - 20}px;
            background: #333;
            padding: 8px 12px;
            border-radius: 4px;
            color: white;
            z-index: 9999;
            pointer-events: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        `;
        document.body.appendChild(clone);
        this.dragHandleClone = clone;

        console.log('🔧 Drag handle touch start:', channel.key);
    }

    onDragHandleTouchMove(event: TouchEvent): void {
        if (!this.dragHandleChannel || !this.dragHandleClone) return;

        event.preventDefault();
        const touch = event.touches[0];
        this.dragHandleClone.style.left = `${touch.clientX - 30}px`;
        this.dragHandleClone.style.top = `${touch.clientY - 20}px`;

        // Highlight action buttons on hover
        const elements = document.elementsFromPoint(touch.clientX, touch.clientY);
        const hangupBtn = elements.find(el => el.classList.contains('hangup-btn'));
        const trashBtn = elements.find(el => el.classList.contains('trash-btn'));

        // Reset highlights
        document.querySelectorAll('.channel-action-btn').forEach(btn => btn.classList.remove('drag-over'));

        if (hangupBtn) {
            hangupBtn.classList.add('drag-over');
            this.dragOverActionBtn = 'hangup';
        } else if (trashBtn) {
            trashBtn.classList.add('drag-over');
            this.dragOverActionBtn = 'trash';
        } else {
            this.dragOverActionBtn = null;
        }
    }

    onDragHandleTouchEnd(event: TouchEvent): void {
        if (!this.dragHandleChannel) return;

        // Remove clone
        if (this.dragHandleClone) {
            this.dragHandleClone.remove();
            this.dragHandleClone = null;
        }

        // Check drop target
        const touch = event.changedTouches[0];
        const elements = document.elementsFromPoint(touch.clientX, touch.clientY);
        const hangupBtn = elements.find(el => el.classList.contains('hangup-btn'));
        const trashBtn = elements.find(el => el.classList.contains('trash-btn'));

        if (hangupBtn) {
            console.log('📴 Drag hangup:', this.dragHandleChannel.key);
            this.hangupChannel(this.dragHandleChannel);
        } else if (trashBtn) {
            console.log('🗑️ Drag delete:', this.dragHandleChannel.key);
            this.deleteChannel(this.dragHandleChannel);
        }

        // Reset
        this.dragHandleChannel = null;
        this.dragOverActionBtn = null;
        document.querySelectorAll('.channel-action-btn').forEach(btn => btn.classList.remove('drag-over'));
    }

    // Hangup specific channel
    hangupChannel(channel: Channel): void {
        const index = this.channels.findIndex(c => c.key === channel.key);
        if (index !== -1) {
            this.sipService.hangupCall(channel.key);
            this.channels[index] = {
                ...this.channels[index],
                isActive: false,
                isCalling: false,
                isPtt: false
            };
            this.channels = [...this.channels];
            console.log('📴 Hangup channel:', channel.key);
        }
    }

    // Delete/clear channel assignment
    deleteChannel(channel: Channel): void {
        const index = this.channels.findIndex(c => c.key === channel.key);
        if (index !== -1) {
            // Hangup first if active
            if (channel.isActive || channel.isCalling) {
                this.sipService.hangupCall(channel.key);
            }
            // Clear assignment
            this.channels[index] = {
                ...this.channels[index],
                name: '',
                extension: '',
                isActive: false,
                isCalling: false,
                isPtt: false
            };
            this.channels = [...this.channels];
            console.log('🗑️ Deleted channel:', channel.key);
        }
    }

    // ============================================
    // DRAG & DROP: LINE/VPW/CAS ITEMS
    // ============================================
    handleLineVPWCASDragStart(event: DragEvent, itemName: string, itemType: string, itemId: string): void {
        const dragData = JSON.stringify({
            type: 'line_vpw_cas_item',
            name: itemName,
            itemType: itemType,
            itemId: itemId
        });

        if (event.dataTransfer) {
            event.dataTransfer.setData('application/json', dragData);
            event.dataTransfer.setData('text/plain', dragData);
            event.dataTransfer.effectAllowed = 'copy';
        }
        console.log(`📦 Dragging ${itemType}: ${itemName}`);
    }

    // Touch drag for Line/VPW/CAS items
    handleDrawerTouchStart(event: TouchEvent, itemName: string, itemType: string, itemId: string): void {
        const touch = event.touches[0];
        const target = event.currentTarget as HTMLElement;

        this.touchDragData = {
            contactName: itemName,
            phone: itemId, // Use itemId as identifier
            startX: touch.clientX,
            startY: touch.clientY,
            isDragging: false,
            clone: undefined
        };

        // Store itemType for later use
        (this.touchDragData as any).itemType = itemType;
    }

    handleDrawerTouchMove(event: TouchEvent): void {
        if (!this.touchDragData) return;

        const touch = event.touches[0];
        const deltaX = Math.abs(touch.clientX - this.touchDragData.startX);
        const deltaY = Math.abs(touch.clientY - this.touchDragData.startY);

        // Start drag if moved more than 10px
        if (!this.touchDragData.isDragging && (deltaX > 10 || deltaY > 10)) {
            this.touchDragData.isDragging = true;

            // Create clone
            const clone = document.createElement('div');
            clone.className = 'touch-drag-clone';
            clone.textContent = this.touchDragData.contactName;
            clone.style.cssText = `
                position: fixed;
                left: ${touch.clientX}px;
                top: ${touch.clientY}px;
                transform: translate(-50%, -50%);
                background: var(--color-accent-green);
                color: white;
                padding: 8px 16px;
                border-radius: 8px;
                z-index: 9999;
                pointer-events: none;
                font-size: 14px;
            `;
            document.body.appendChild(clone);
            this.touchDragData.clone = clone;
        }

        // Move clone
        if (this.touchDragData.isDragging && this.touchDragData.clone) {
            this.touchDragData.clone.style.left = `${touch.clientX}px`;
            this.touchDragData.clone.style.top = `${touch.clientY}px`;

            // Highlight drop targets (channel cells)
            document.querySelectorAll('.call-cell').forEach(cell => {
                const rect = cell.getBoundingClientRect();
                if (touch.clientX >= rect.left && touch.clientX <= rect.right &&
                    touch.clientY >= rect.top && touch.clientY <= rect.bottom) {
                    cell.classList.add('drag-over');
                } else {
                    cell.classList.remove('drag-over');
                }
            });
        }

        event.preventDefault();
    }

    handleDrawerTouchEnd(event: TouchEvent): void {
        if (!this.touchDragData) return;

        // Remove clone
        if (this.touchDragData.clone) {
            this.touchDragData.clone.remove();
        }

        if (this.touchDragData.isDragging) {
            const touch = event.changedTouches[0];
            const elements = document.elementsFromPoint(touch.clientX, touch.clientY);
            const channelCell = elements.find(el => el.classList.contains('call-cell')) as HTMLElement;

            if (channelCell) {
                const channelKey = channelCell.dataset['channelKey'];
                const targetChannel = this.channels.find(c => c.key === channelKey);

                if (targetChannel) {
                    console.log(`📞 Touch drop ${this.touchDragData.contactName} to ${channelKey}`);
                    targetChannel.name = this.touchDragData.contactName;
                    targetChannel.extension = this.touchDragData.phone;

                    // [FIX] Auto-select channel after drop
                    this.channels.forEach(ch => ch.isSelected = false);
                    targetChannel.isSelected = true;
                    this.selectedChannel = targetChannel;

                    this.channels = [...this.channels];

                    // Close drawer
                    const lineDrawer = document.getElementById('lineListDrawer');
                    const vpwDrawer = document.getElementById('vpwListDrawer');
                    const casDrawer = document.getElementById('casListDrawer');
                    [lineDrawer, vpwDrawer, casDrawer].forEach(drawer => {
                        if (drawer && bootstrap?.Offcanvas) {
                            bootstrap.Offcanvas.getInstance(drawer)?.hide();
                        }
                    });
                }
            }

            // Clear drag-over styling
            document.querySelectorAll('.call-cell').forEach(cell => cell.classList.remove('drag-over'));
        }

        this.touchDragData = null;
    }


    // ============================================
    // DRAG & DROP: DASHBOARD SLOTS (24-slot layout)
    // ============================================
    draggedSlotIndex: number | null = null;
    dragOverSlotIndex: number | null = null;

    onSlotDragStart(event: DragEvent, slotIndex: number): void {
        this.draggedSlotIndex = slotIndex;
        const icon = this.dashboardLayout[slotIndex];
        if (event.dataTransfer && icon) {
            event.dataTransfer.setData('text/plain', String(slotIndex));
            event.dataTransfer.setData('application/json', JSON.stringify({
                type: 'dashboard_slot',
                slotIndex: slotIndex,
                iconId: icon.id
            }));
            event.dataTransfer.effectAllowed = 'move';
        }
        // Visual feedback
        (event.target as HTMLElement).closest('.dashboard-icon-item')?.classList.add('dragging');
        console.log('Drag start slot:', slotIndex, icon?.id);
    }

    onSlotDragEnd(event: DragEvent): void {
        // Cleanup all drag states
        document.querySelectorAll('.dashboard-icon-item.dragging').forEach(el => el.classList.remove('dragging'));
        document.querySelectorAll('.dashboard-icon-item.drag-over').forEach(el => el.classList.remove('drag-over'));
        document.querySelectorAll('.dashboard-empty-cell.drag-over').forEach(el => el.classList.remove('drag-over'));
        this.draggedSlotIndex = null;
        this.dragOverSlotIndex = null;
    }

    onSlotDragOver(event: DragEvent, slotIndex: number): void {
        event.preventDefault();
        if (event.dataTransfer) event.dataTransfer.dropEffect = 'move';

        // Visual feedback on target slot
        if (this.dragOverSlotIndex !== slotIndex) {
            document.querySelectorAll('.dashboard-icon-item.drag-over').forEach(el => el.classList.remove('drag-over'));
            document.querySelectorAll('.dashboard-empty-cell.drag-over').forEach(el => el.classList.remove('drag-over'));
            const target = (event.target as HTMLElement).closest('.dashboard-icon-item, .dashboard-empty-cell');
            target?.classList.add('drag-over');
            this.dragOverSlotIndex = slotIndex;
        }
    }

    onSlotDragLeave(event: DragEvent): void {
        const target = (event.target as HTMLElement).closest('.dashboard-icon-item, .dashboard-empty-cell');
        target?.classList.remove('drag-over');
    }

    onSlotDrop(event: DragEvent, targetSlotIndex: number): void {
        event.preventDefault();

        // Cleanup visual states
        document.querySelectorAll('.dashboard-icon-item.dragging').forEach(el => el.classList.remove('dragging'));
        document.querySelectorAll('.dashboard-icon-item.drag-over').forEach(el => el.classList.remove('drag-over'));
        document.querySelectorAll('.dashboard-empty-cell.drag-over').forEach(el => el.classList.remove('drag-over'));

        if (this.draggedSlotIndex !== null && this.draggedSlotIndex !== targetSlotIndex) {
            // SWAP slots - move icon from source to target
            const sourceIcon = this.dashboardLayout[this.draggedSlotIndex];
            const targetIcon = this.dashboardLayout[targetSlotIndex];

            // Swap contents
            if (this.activeTab) {
                this.activeTab.items[targetSlotIndex] = sourceIcon;
                this.activeTab.items[this.draggedSlotIndex] = targetIcon;
                this.tabs = [...this.tabs];  // Trigger change detection
            }

            console.log(`Swapped slot ${this.draggedSlotIndex} with slot ${targetSlotIndex}`);
        }

        this.draggedSlotIndex = null;
        this.dragOverSlotIndex = null;
    }

    // ============================================
    // DRAG & DROP: TRASH
    // ============================================
    onTrashDragOver(event: DragEvent): void {
        event.preventDefault();
        this.dragOverTrash = true;
    }

    onTrashDragLeave(event: DragEvent): void {
        this.dragOverTrash = false;
    }

    onTrashDrop(event: DragEvent): void {
        event.preventDefault();
        this.dragOverTrash = false;

        // Delete dragged channel
        if (this.draggedChannel) {
            const index = this.channels.findIndex(c => c.key === this.draggedChannel!.key);
            if (index !== -1) {
                this.channels[index] = {
                    ...this.channels[index],
                    name: `Ch ${index + 1}`,
                    extension: '',
                    isActive: false,
                    groups: []
                };
                console.log('Deleted channel:', this.draggedChannel.key);
            }
            this.draggedChannel = null;
        }

        // Handle fav_item_reorder drop (from favourite grid in edit mode)
        try {
            const jsonData = event.dataTransfer?.getData('application/json');
            if (jsonData) {
                const data = JSON.parse(jsonData);
                if (data.type === 'fav_item_reorder' && this.activeTab) {
                    const sourceIndex = data.sourceIndex;
                    if (sourceIndex !== null && sourceIndex !== undefined) {
                        const item = this.activeTab.items[sourceIndex];
                        if (item) {
                            this.activeTab.items[sourceIndex] = null;
                            this.tabs = [...this.tabs];
                            console.log('🗑️ Deleted fav item:', item.label, 'from slot', sourceIndex);
                            this.draggedFavIndex = null;
                            return;
                        }
                    }
                }
            }
        } catch (e) {
            console.log('Could not parse drop data');
        }

        // Delete dragged dashboard icon (only if deletable - favourites only)
        if (this.draggedSlotIndex !== null && this.activeTab) {
            const icon = this.activeTab.items[this.draggedSlotIndex];
            if (icon && icon.isDeletable) {
                this.activeTab.items[this.draggedSlotIndex] = null;
                this.tabs = [...this.tabs];  // Trigger change detection
                console.log('Deleted icon:', icon.id, 'from slot', this.draggedSlotIndex);
            } else if (icon) {
                console.log('Cannot delete core icon:', icon.id);
            }
            this.draggedSlotIndex = null;
        }
    }

    // ============================================
    // FAVOURITE SLOT DRAG & DROP (From Line/VPW/CAS/Phonebook)
    // ============================================
    onFavouriteSlotDragOver(event: DragEvent, slotIndex: number): void {
        event.preventDefault();
        this.dragOverEmptyCell = slotIndex;
        (event.target as HTMLElement)?.classList.add('drag-over');
    }

    onFavouriteSlotDrop(event: DragEvent, slotIndex: number): void {
        event.preventDefault();
        this.dragOverEmptyCell = null;
        (event.target as HTMLElement)?.classList.remove('drag-over');

        if (!this.activeTab || this.activeTabId === 'dashboard') return;

        try {
            const jsonData = event.dataTransfer?.getData('application/json') || event.dataTransfer?.getData('text/plain');
            if (!jsonData) return;

            const data = JSON.parse(jsonData);
            console.log('📥 Favourite Drop:', data);

            let newItem: DashboardIcon | null = null;

            // Handle fav item reorder (move to empty slot)
            if (data.type === 'fav_item_reorder') {
                const sourceIndex = data.sourceIndex;
                if (sourceIndex !== null && sourceIndex !== slotIndex) {
                    const sourceItem = this.activeTab.items[sourceIndex];
                    if (sourceItem) {
                        this.activeTab.items[slotIndex] = sourceItem;
                        this.activeTab.items[sourceIndex] = null;
                        this.tabs = [...this.tabs];
                        console.log(`✅ Moved item from ${sourceIndex} to empty slot ${slotIndex}`);
                        this.draggedFavIndex = null;
                    }
                }
                return;
            }

            // Handle Line/VPW/CAS drops
            if (data.type === 'line_vpw_cas_item') {
                let icon = 'call';
                let color = 'var(--color-accent-green)';

                if (data.itemType === 'line') {
                    icon = 'call';
                    color = 'var(--color-accent-green)';
                } else if (data.itemType === 'vpw') {
                    icon = 'private_connectivity';
                    color = 'var(--color-accent-red)';
                } else if (data.itemType === 'cas') {
                    icon = 'campaign';
                    color = 'var(--color-accent-blue)';
                }

                newItem = {
                    id: `${data.itemType}_${data.itemId}`,
                    label: data.name,
                    icon: icon,
                    color: color,
                    bgColor: '#333',
                    isDeletable: true
                };
            }
            // Handle Phonebook contact drops
            else if (data.type === 'phonebook') {
                newItem = {
                    id: `contact_${data.id}`,
                    label: data.name,
                    icon: 'contact_page',
                    color: 'var(--color-accent-green)',
                    bgColor: '#333',
                    isDeletable: true
                };
            }

            if (newItem) {
                // Check for duplicates by NAME OR PHONE (if both defined)
                // Bug fix: undefined === undefined returns true, so check existence first
                const isDuplicate = this.activeTab.items.some(item => {
                    if (!item) return false;
                    // Check by label
                    if (item.label === newItem!.label) return true;
                    // Check by phone (only if BOTH have phone defined)
                    const itemPhone = (item as any)?.phone;
                    const newPhone = data.phone;
                    if (itemPhone && newPhone && itemPhone === newPhone) return true;
                    return false;
                });
                if (isDuplicate) {
                    console.log('⚠️ Item already in favourite:', newItem.label);
                    this.duplicateModalMessage = `"${newItem.label}" already listed on this Favourite`;
                    this.showDuplicateModal = true;
                    return;
                }

                // Store phone in the item for future duplicate checks
                (newItem as any).phone = data.phone;

                // Add to slot
                this.activeTab.items[slotIndex] = newItem;
                this.tabs = [...this.tabs]; // Trigger change detection
                console.log(`✅ Added ${newItem.label} to favourite slot ${slotIndex}`);
            }
        } catch (e) {
            console.error('Favourite drop error:', e);
        }
    }

    // ============================================
    // FAVOURITE ITEM DRAG (For reordering filled items)
    // ============================================
    private draggedFavIndex: number | null = null;

    onFavItemDragStart(event: DragEvent, index: number): void {
        this.draggedFavIndex = index;
        const item = this.activeTab?.items[index];

        // Check if in edit mode
        const isEditMode = this.activeTab?.isEditMode || false;

        // Edit mode = reorder/delete within grid
        // Non-edit mode = copy to channel
        const dragType = isEditMode ? 'fav_item_reorder' : 'favourite_to_channel';

        if (event.dataTransfer && item) {
            event.dataTransfer.setData('application/json', JSON.stringify({
                type: dragType,
                sourceIndex: index,
                itemId: item.id,
                name: item.label,
                phone: (item as any).phone || item.id
            }));
            event.dataTransfer.effectAllowed = isEditMode ? 'move' : 'copy';
        }

        if (isEditMode) {
            (event.target as HTMLElement)?.classList.add('dragging');
        }
        console.log(`🔄 Fav item drag start (${dragType}):`, index, item?.label);
    }

    onFavItemDragOver(event: DragEvent, index: number): void {
        event.preventDefault();
        if (event.dataTransfer) event.dataTransfer.dropEffect = 'move';
        (event.target as HTMLElement)?.closest('.favourite-item')?.classList.add('drag-over');
    }

    onFavItemDrop(event: DragEvent, targetIndex: number): void {
        event.preventDefault();
        (event.target as HTMLElement)?.closest('.favourite-item')?.classList.remove('drag-over');
        document.querySelectorAll('.favourite-item.dragging').forEach(el => el.classList.remove('dragging'));

        if (!this.activeTab || this.draggedFavIndex === null || this.draggedFavIndex === targetIndex) {
            this.draggedFavIndex = null;
            return;
        }

        // SWAP items
        const sourceItem = this.activeTab.items[this.draggedFavIndex];
        const targetItem = this.activeTab.items[targetIndex];

        this.activeTab.items[targetIndex] = sourceItem;
        this.activeTab.items[this.draggedFavIndex] = targetItem;
        this.tabs = [...this.tabs]; // Trigger change detection

        console.log(`✅ Swapped favourite items: ${this.draggedFavIndex} <-> ${targetIndex}`);
        this.draggedFavIndex = null;
    }

    // ============================================
    // CHANNEL ACTION BAR DRAG
    // ============================================
    deleteSelected(): void {
        if (this.selectedChannel) {
            const index = this.channels.findIndex(c => c.key === this.selectedChannel!.key);
            if (index !== -1) {
                this.channels[index] = {
                    ...this.channels[index],
                    name: `Ch ${index + 1}`,
                    extension: '',
                    isActive: false,
                    groups: []
                };
                console.log('Deleted selected channel:', this.selectedChannel.key);
                this.selectedChannel = null;
            }
        }
    }

    onActionBtnDragOver(event: DragEvent, action: string): void {
        event.preventDefault();
        this.dragOverActionBtn = action;
    }

    onActionBtnDragLeave(event: DragEvent): void {
        this.dragOverActionBtn = null;
    }

    onActionBtnDrop(event: DragEvent, action: string): void {
        event.preventDefault();
        this.dragOverActionBtn = null;

        // Get channel from drag handle or dragged channel
        let channel: Channel | null = this.dragHandleChannel || this.draggedChannel;

        // Also check dataTransfer for channel-key
        if (!channel && event.dataTransfer) {
            const channelKey = event.dataTransfer.getData('channel-key');
            if (channelKey) {
                channel = this.channels.find(c => c.key === channelKey) || null;
            }
        }

        if (!channel) return;

        if (action === 'hangup') {
            console.log('📴 Hangup via drag:', channel.key);
            this.hangupChannel(channel);
        } else if (action === 'trash') {
            console.log('🗑️ Delete via drag:', channel.key);
            this.deleteChannel(channel);
        }

        this.draggedChannel = null;
        this.dragHandleChannel = null;
    }

    // ============================================
    // VIRTUAL KEYBOARD METHODS
    // ============================================
    showKeyboard(inputEl: HTMLInputElement): void {
        this.vkInput = inputEl;
        this.vkActive = true;
        this.vkSymbols = false;
        this.vkFirstKey = true;  // [FIX] Mark that next key should replace all

        // [FIX] Restore text selection after touch event (like SmartX)
        setTimeout(() => {
            if (this.vkInput) {
                this.vkInput.focus();
                this.vkInput.select();
            }
        }, 50);
    }

    hideKeyboard(): void {
        this.vkActive = false;
        this.vkInput = null;
        this.vkShift = false;
        this.vkSymbols = false;
    }

    typeKey(char: string): void {
        if (this.vkInput) {
            const value = this.vkInput.value;

            // [FIX] On first key after VK opens, replace all text
            // This handles the case where touch deselects text before typeKey runs
            if (this.vkFirstKey && value.length > 0) {
                this.vkInput.value = char;
                this.vkInput.selectionStart = this.vkInput.selectionEnd = 1;
                this.vkFirstKey = false;
            } else {
                // Normal typing - use actual selection (handle 0 properly)
                const start = this.vkInput.selectionStart ?? value.length;
                const end = this.vkInput.selectionEnd ?? value.length;

                if (start !== end) {
                    // Text is selected - replace it
                    this.vkInput.value = value.substring(0, start) + char + value.substring(end);
                } else {
                    // No selection - insert at cursor
                    this.vkInput.value = value.substring(0, start) + char + value.substring(start);
                }
                this.vkInput.selectionStart = this.vkInput.selectionEnd = start + 1;
            }

            this.vkInput.focus();
            // Trigger input event for Angular binding
            this.vkInput.dispatchEvent(new Event('input'));
        }
    }

    backspace(): void {
        if (this.vkInput && this.vkInput.value.length > 0) {
            const start = this.vkInput.selectionStart || this.vkInput.value.length;
            const end = this.vkInput.selectionEnd || this.vkInput.value.length;
            const value = this.vkInput.value;
            if (start === end && start > 0) {
                // No selection, delete char before cursor
                this.vkInput.value = value.substring(0, start - 1) + value.substring(end);
                this.vkInput.selectionStart = this.vkInput.selectionEnd = start - 1;
            } else {
                // Selection, delete selected text
                this.vkInput.value = value.substring(0, start) + value.substring(end);
                this.vkInput.selectionStart = this.vkInput.selectionEnd = start;
            }
            this.vkInput.dispatchEvent(new Event('input'));
        }
    }

    toggleVkShift(): void {
        this.vkShift = !this.vkShift;
    }

    toggleVkSymbols(): void {
        this.vkSymbols = !this.vkSymbols;
    }

    // ============================================
    // LOGIN METHODS
    // ============================================
    processLogin(): void {
        this.loginStatus = 'Connecting to PBX...';
        this.loginStatusColor = '#ffcc00';
        this.hideKeyboard();

        // [FIX] Register SIP extension on login
        // Using hardcoded config for now - should come from API
        this.sipService.registerExtension({
            server: '103.154.80.172',
            port: 8089,
            extension: '6001',  // Default extension
            password: 'Maja1234'  // Should be from API
        });

        // Wait for registration then enter dashboard
        const sub = this.sipService.registrationStatus$.subscribe(status => {
            if (status === 'registered') {
                this.loginStatus = 'Connected!';
                this.loginStatusColor = '#00ff66';
                setTimeout(() => {
                    this.showLogin = false;
                    console.log('✅ SIP Registered, entered dashboard');
                }, 500);
                sub.unsubscribe();
            } else if (status === 'failed') {
                this.loginStatus = 'Connection Failed';
                this.loginStatusColor = '#ff4d4d';
                sub.unsubscribe();
            }
        });

        // Timeout fallback - enter anyway after 5s
        setTimeout(() => {
            if (this.showLogin) {
                this.loginStatus = 'Entering (Offline Mode)...';
                this.showLogin = false;
                console.log('⚠️ SIP Registration timeout, entering offline mode');
            }
        }, 5000);
    }
}

