<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Topology Container --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Network Topology Map</h2>
                <div class="flex gap-4 text-sm">
                    <span class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-blue-500"></span>
                        <span class="text-gray-600 dark:text-gray-400">Head Office</span>
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-green-500"></span>
                        <span class="text-gray-600 dark:text-gray-400">Branch (Active)</span>
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-red-500"></span>
                        <span class="text-gray-600 dark:text-gray-400">Branch (Offline)</span>
                    </span>
                </div>
            </div>
            <div id="topology-network"
                style="width: 100%; height: 600px; border: 1px solid #e5e7eb; border-radius: 8px; background: #f9f9f9;">
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Head Offices</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white" id="stat-ho">-</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Branches</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white" id="stat-branches">-</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="text-sm text-gray-500 dark:text-gray-400">Active Connections</div>
                <div class="text-2xl font-bold text-green-500" id="stat-connections">-</div>
            </div>
        </div>
    </div>

    {{-- Load vis.js from CDN --}}
    <script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const topologyData = @json($topologyData);

            // Count stats
            let hoCount = 0, branchCount = 0, connectionCount = 0;

            // Icon URLs - local PNG files
            const headOfficeIcon = '/images/topology/head-office.png';
            const branchIcon = '/images/topology/branch.png';
            const branchSbcIcon = '/images/topology/branch-sbc.png';

            // Transform nodes for vis.js with proper icons
            const nodes = topologyData.nodes.map(node => {
                if (node.group === 'headoffice') {
                    hoCount++;
                    return {
                        id: node.id,
                        label: node.label + '\ntype: peer\nIP:',
                        title: node.title,
                        shape: 'image',
                        image: headOfficeIcon,
                        size: 45,
                        font: { color: '#374151', size: 11, face: 'arial', multi: true, align: 'left' }
                    };
                } else if (node.group === 'standalone') {
                    branchCount++;
                    return {
                        id: node.id,
                        label: node.label + '\ntype: standalone\nIP: ' + node.ip,
                        title: node.title,
                        shape: 'image',
                        image: branchIcon,
                        size: 40,
                        font: { color: '#374151', size: 10, face: 'arial', multi: true }
                    };
                } else {
                    branchCount++;
                    const isActive = node.status === 'OK';
                    const hasSbc = node.has_sbc || false;
                    return {
                        id: node.id,
                        label: node.label + '\ntype: peer\nIP: ' + node.ip + '\n' + (isActive ? 'OK' : 'Offline'),
                        title: node.title,
                        shape: 'image',
                        image: hasSbc ? branchSbcIcon : branchIcon,
                        size: 40,
                        font: { color: isActive ? '#16a34a' : '#dc2626', size: 10, face: 'arial', multi: true }
                    };
                }
            });

            // Transform edges for vis.js
            const edges = topologyData.edges.map(edge => {
                connectionCount++;
                return {
                    from: edge.from,
                    to: edge.to,
                    label: edge.label,
                    color: { color: edge.color, highlight: edge.color },
                    width: 2,
                    font: { color: edge.color, size: 14, strokeWidth: 0 },
                    smooth: { type: 'curvedCW', roundness: 0.2 }
                };
            });

            // Update stats
            document.getElementById('stat-ho').textContent = hoCount;
            document.getElementById('stat-branches').textContent = branchCount;
            document.getElementById('stat-connections').textContent = connectionCount;

            // Create network
            const container = document.getElementById('topology-network');
            const data = { nodes: new vis.DataSet(nodes), edges: new vis.DataSet(edges) };

            const options = {
                nodes: {
                    borderWidth: 2,
                    shadow: true
                },
                edges: {
                    shadow: true,
                    arrows: { to: { enabled: false } }
                },
                physics: {
                    enabled: true,
                    hierarchicalRepulsion: {
                        centralGravity: 0.0,
                        springLength: 200,
                        springConstant: 0.01,
                        nodeDistance: 150
                    },
                    solver: 'hierarchicalRepulsion'
                },
                layout: {
                    hierarchical: {
                        enabled: true,
                        direction: 'LR',
                        sortMethod: 'directed',
                        levelSeparation: 250,
                        nodeSpacing: 100
                    }
                },
                interaction: {
                    hover: true,
                    tooltipDelay: 200
                }
            };

            const network = new vis.Network(container, data, options);

            // Click handler - navigate to edit page
            network.on('click', function (params) {
                if (params.nodes.length > 0) {
                    const nodeId = params.nodes[0];
                    if (nodeId.startsWith('ho_')) {
                        const id = nodeId.replace('ho_', '');
                        window.location.href = '/admin/head-offices/' + id + '/edit';
                    } else if (nodeId.startsWith('br_')) {
                        const id = nodeId.replace('br_', '');
                        window.location.href = '/admin/branches/' + id + '/edit';
                    }
                }
            });
        });
    </script>

    {{-- Font Awesome for icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</x-filament-panels::page>