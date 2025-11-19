<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Grafo de Trazabilidad - Diagrama de Flujo de Dependencias
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Proyecto: <span class="font-mono font-bold">{{ $proyecto->nombre }}</span>
                </p>
            </div>
            <a href="{{ route('proyectos.show', $proyecto) }}" class="btn btn-ghost btn-sm">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Controles y Leyenda -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
                <!-- Botones de Vista -->
                <div class="lg:col-span-3 card bg-white shadow-md border border-gray-200">
                    <div class="card-body p-4">
                        <h3 class="text-lg font-bold text-gray-800 mb-3">Tipo de Visualización</h3>
                        <div class="flex flex-wrap gap-2">
                            <button id="btnSankey" class="btn btn-sm btn-primary">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Sankey (Flujo)
                            </button>
                            <button id="btnJerarquico" class="btn btn-sm btn-outline btn-primary">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Jerárquico
                            </button>
                            <button id="btnCircular" class="btn btn-sm btn-outline btn-primary">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Radial
                            </button>
                            <button id="btnExportar" class="btn btn-sm btn-accent ml-auto">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Exportar PNG
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="card bg-gradient-to-br from-blue-500 to-purple-600 text-white shadow-lg">
                    <div class="card-body p-4">
                        <h3 class="font-bold text-sm mb-2 opacity-90">Estadísticas</h3>
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span>Elementos:</span>
                                <span id="statsNodos" class="font-bold">0</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Relaciones:</span>
                                <span id="statsRelaciones" class="font-bold">0</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Niveles:</span>
                                <span id="statsNiveles" class="font-bold">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leyenda de Tipos de EC -->
            <!-- Leyenda de Tipos de EC -->
            <div class="card bg-white shadow-md border border-gray-200 mb-6">
                <div class="card-body p-4">
                    <h3 class="font-bold text-black mb-3">Leyenda de Tipos de Elementos</h3>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded" style="background-color: #3b82f6;"></div>
                            <span class="text-black">DOCUMENTO</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded" style="background-color: #10b981;"></div>
                            <span class="text-black">CODIGO</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded" style="background-color: #f59e0b;"></div>
                            <span class="text-black">SCRIPT_BD</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded" style="background-color: #8b5cf6;"></div>
                            <span class="text-black">CONFIGURACION</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded" style="background-color: #6b7280;"></div>
                            <span class="text-black">OTRO</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenedor del Grafo -->
            <div class="card bg-white shadow-xl border-2 border-gray-300">
                <div class="card-body p-6">
                    <div class="relative">
                        <!-- Sankey Diagram -->
                        <div id="sankey-container" style="width: 100%; height: 700px;"></div>

                        <!-- Otros grafos (ocultos inicialmente) -->
                        <div id="ec-graph" style="height: 700px; width: 100%; display:none;"></div>
                        <div id="cy-graph" style="height: 700px; width: 100%; display:none;"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- CDN de librerías -->
    <link href="https://unpkg.com/vis-network/styles/vis-network.min.css" rel="stylesheet" />
    <script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
    <script src="https://unpkg.com/cytoscape/dist/cytoscape.min.js"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/d3-sankey@0.12.3/dist/d3-sankey.min.js"></script>

    @push('scripts')
    <script>
        let network = null;
        let nodes = null;
        let edges = null;
        let rawNodes = null;
        let rawEdges = null;
        let cy = null;
        let sankeyData = null;

        // Colores por tipo de EC
        const tipoColores = {
            'DOCUMENTO': '#3b82f6',
            'CODIGO': '#10b981',
            'SCRIPT_BD': '#f59e0b',
            'CONFIGURACION': '#8b5cf6',
            'OTRO': '#6b7280'
        };

        // Cargar datos
        fetch("{{ route('proyectos.elementos.grafo', ['proyecto' => $proyecto->id]) }}")
            .then(response => response.json())
            .then(data => {
                rawNodes = data.nodes;
                rawEdges = data.edges;
                nodes = new vis.DataSet(data.nodes);
                edges = new vis.DataSet(data.edges);

                // Actualizar estadísticas
                document.getElementById('statsNodos').textContent = data.nodes.length;
                document.getElementById('statsRelaciones').textContent = data.edges.length;

                // Dibujar Sankey por defecto
                drawSankey();
            });

        function drawSankey() {
            // Ocultar otros grafos
            document.getElementById('ec-graph').style.display = 'none';
            document.getElementById('cy-graph').style.display = 'none';
            document.getElementById('sankey-container').style.display = 'block';

            // Limpiar contenedor
            d3.select('#sankey-container').selectAll('*').remove();

            if (!rawNodes || !rawEdges || rawNodes.length === 0) {
                d3.select('#sankey-container')
                    .append('div')
                    .attr('class', 'flex items-center justify-center h-full text-gray-500')
                    .html('<p>No hay elementos para visualizar</p>');
                return;
            }

            // Preparar datos para Sankey
            const nodeMap = new Map();
            rawNodes.forEach((n, i) => {
                nodeMap.set(n.id, {
                    ...n,
                    index: i,
                    name: n.codigo || n.label
                });
            });

            const sankeyNodes = rawNodes.map((n, i) => ({
                name: n.codigo || n.label,
                id: n.id,
                tipo: n.group,
                estado: n.estado,
                versiones: n.versiones
            }));

            const sankeyLinks = rawEdges.map(e => {
                const source = nodeMap.get(e.from)?.index;
                const target = nodeMap.get(e.to)?.index;
                if (source !== undefined && target !== undefined) {
                    return {
                        source,
                        target,
                        value: 1,
                        label: e.label
                    };
                }
                return null;
            }).filter(l => l !== null);

            // Configuración del Sankey
            const width = document.getElementById('sankey-container').offsetWidth;
            const height = 700;
            const margin = { top: 20, right: 200, bottom: 20, left: 200 };

            const svg = d3.select('#sankey-container')
                .append('svg')
                .attr('width', width)
                .attr('height', height)
                .attr('viewBox', [0, 0, width, height]);

            const sankey = d3.sankey()
                .nodeId(d => d.index)
                .nodeWidth(20)
                .nodePadding(15)
                .extent([[margin.left, margin.top], [width - margin.right, height - margin.bottom]]);

            const { nodes: sankeyNodesPositioned, links: sankeyLinksPositioned } = sankey({
                nodes: sankeyNodes.map(d => Object.assign({}, d)),
                links: sankeyLinks.map(d => Object.assign({}, d))
            });

            // Gradientes para los links
            const defs = svg.append('defs');
            sankeyLinksPositioned.forEach((link, i) => {
                const gradient = defs.append('linearGradient')
                    .attr('id', `gradient-${i}`)
                    .attr('gradientUnits', 'userSpaceOnUse')
                    .attr('x1', link.source.x1)
                    .attr('x2', link.target.x0);

                gradient.append('stop')
                    .attr('offset', '0%')
                    .attr('stop-color', tipoColores[link.source.tipo] || '#999')
                    .attr('stop-opacity', 0.6);

                gradient.append('stop')
                    .attr('offset', '100%')
                    .attr('stop-color', tipoColores[link.target.tipo] || '#999')
                    .attr('stop-opacity', 0.6);
            });

            // Dibujar links
            const link = svg.append('g')
                .attr('fill', 'none')
                .selectAll('g')
                .data(sankeyLinksPositioned)
                .join('g');

            link.append('path')
                .attr('d', d3.sankeyLinkHorizontal())
                .attr('stroke', (d, i) => `url(#gradient-${i})`)
                .attr('stroke-width', d => Math.max(1, d.width))
                .style('mix-blend-mode', 'multiply')
                .append('title')
                .text(d => `${d.source.name} → ${d.target.name}\n${d.label || 'Relación'}`);

            // Dibujar nodos
            const node = svg.append('g')
                .selectAll('rect')
                .data(sankeyNodesPositioned)
                .join('rect')
                .attr('x', d => d.x0)
                .attr('y', d => d.y0)
                .attr('height', d => d.y1 - d.y0)
                .attr('width', d => d.x1 - d.x0)
                .attr('fill', d => tipoColores[d.tipo] || '#999')
                .attr('stroke', '#fff')
                .attr('stroke-width', 2)
                .attr('rx', 3)
                .style('cursor', 'pointer')
                .on('mouseover', function() {
                    d3.select(this).attr('opacity', 0.8);
                })
                .on('mouseout', function() {
                    d3.select(this).attr('opacity', 1);
                });

            node.append('title')
                .text(d => `${d.name}\nTipo: ${d.tipo}\nEstado: ${d.estado}\nVersiones: ${d.versiones}`);

            // Labels de nodos
            svg.append('g')
                .style('font', '12px sans-serif')
                .selectAll('text')
                .data(sankeyNodesPositioned)
                .join('text')
                .attr('x', d => d.x0 < width / 2 ? d.x1 + 6 : d.x0 - 6)
                .attr('y', d => (d.y1 + d.y0) / 2)
                .attr('dy', '0.35em')
                .attr('text-anchor', d => d.x0 < width / 2 ? 'start' : 'end')
                .text(d => d.name)
                .style('fill', '#1f2937')
                .style('font-weight', 'bold')
                .append('tspan')
                .attr('x', d => d.x0 < width / 2 ? d.x1 + 6 : d.x0 - 6)
                .attr('dy', '1.2em')
                .text(d => `v${d.versiones}`)
                .style('fill', '#6b7280')
                .style('font-size', '10px')
                .style('font-weight', 'normal');

            // Calcular niveles
            const levels = new Set(sankeyNodesPositioned.map(d => d.layer));
            document.getElementById('statsNiveles').textContent = levels.size;
        }

        function drawGraph(layoutType) {
            if (!nodes || !edges) return;

            document.getElementById('sankey-container').style.display = 'none';
            document.getElementById('cy-graph').style.display = 'none';
            document.getElementById('ec-graph').style.display = 'block';

            if (layoutType === 'jerarquico') {
                let options = {
                    edges: {
                        arrows: 'to',
                        font: { align: 'middle' },
                        color: { color: '#94a3b8', highlight: '#3b82f6' },
                        width: 2
                    },
                    nodes: {
                        shape: 'box',
                        font: { size: 14, color: '#fff', bold: true },
                        borderWidth: 2,
                        borderWidthSelected: 3,
                        shadow: true
                    },
                    layout: {
                        hierarchical: {
                            enabled: true,
                            direction: 'UD',
                            sortMethod: 'directed',
                            nodeSpacing: 200,
                            levelSeparation: 150,
                            treeSpacing: 300,
                            parentCentralization: true
                        }
                    },
                    physics: false
                };

                // Aplicar colores a nodos
                const coloredNodes = nodes.map(n => ({
                    ...n,
                    color: {
                        background: tipoColores[n.group] || '#6b7280',
                        border: '#1f2937',
                        highlight: {
                            background: tipoColores[n.group] || '#6b7280',
                            border: '#000'
                        }
                    }
                }));

                let data = { nodes: new vis.DataSet(coloredNodes), edges };
                if (network) network.destroy();
                network = new vis.Network(document.getElementById('ec-graph'), data, options);
            }
        }

        function drawCircular() {
            document.getElementById('sankey-container').style.display = 'none';
            document.getElementById('ec-graph').style.display = 'none';
            document.getElementById('cy-graph').style.display = 'block';

            let nodeArr = rawNodes ? [...rawNodes] : nodes.get();
            let edgeArr = edges.get();
            let cyElements = [];

            // Detectar nodo raíz
            let edgeTargets = new Set(edgeArr.map(e => e.to.toString()));
            let root = nodeArr.find(n => !edgeTargets.has(n.id.toString())) || nodeArr[0];

            nodeArr.forEach(n => {
                cyElements.push({
                    data: {
                        id: n.id.toString(),
                        label: n.codigo || n.label,
                        tipo: n.group
                    },
                    classes: n.id === root.id ? 'central' : n.group.toLowerCase()
                });
            });

            edgeArr.forEach(e => {
                cyElements.push({
                    data: {
                        id: e.from + '_' + e.to,
                        source: e.from.toString(),
                        target: e.to.toString(),
                        label: e.label || ''
                    }
                });
            });

            if (cy) cy.destroy();
            if (cy) cy.destroy();
            cy = cytoscape({
                container: document.getElementById('cy-graph'),
                elements: cyElements,
                style: [
                    {
                        selector: 'node.central',
                        style: {
                            'background-color': '#1f2937',
                            'label': 'data(label)',
                            'color': '#000',
                            'font-size': '24px',
                            'font-weight': '900',
                            'width': '140px',
                            'height': '140px',
                            'text-valign': 'center',
                            'text-halign': 'center',
                            'text-wrap': 'wrap',
                            'text-max-width': '160px',
                            'border-width': 6,
                            'border-color': '#3b82f6',
                            'shape': 'ellipse',
                            'text-outline-color': '#fff',
                            'text-outline-width': 3
                        }
                    },
                    {
                        selector: 'node.documento',
                        style: {
                            'background-color': '#3b82f6',
                            'label': 'data(label)',
                            'color': '#000',
                            'font-size': '18px',
                            'font-weight': '900',
                            'width': '120px',
                            'height': '120px',
                            'text-valign': 'center',
                            'text-halign': 'center',
                            'text-wrap': 'wrap',
                            'text-max-width': '140px',
                            'border-width': 4,
                            'border-color': '#1e40af',
                            'shape': 'ellipse',
                            'text-outline-color': '#fff',
                            'text-outline-width': 3
                        }
                    },
                    {
                        selector: 'node.codigo',
                        style: {
                            'background-color': '#10b981',
                            'label': 'data(label)',
                            'color': '#000',
                            'font-size': '18px',
                            'font-weight': '900',
                            'width': '120px',
                            'height': '120px',
                            'text-valign': 'center',
                            'text-halign': 'center',
                            'text-wrap': 'wrap',
                            'text-max-width': '140px',
                            'border-width': 4,
                            'border-color': '#047857',
                            'shape': 'ellipse',
                            'text-outline-color': '#fff',
                            'text-outline-width': 3
                        }
                    },
                    {
                        selector: 'node.script_bd',
                        style: {
                            'background-color': '#f59e0b',
                            'label': 'data(label)',
                            'color': '#000',
                            'font-size': '18px',
                            'font-weight': '900',
                            'width': '120px',
                            'height': '120px',
                            'text-valign': 'center',
                            'text-halign': 'center',
                            'text-wrap': 'wrap',
                            'text-max-width': '140px',
                            'border-width': 4,
                            'border-color': '#b45309',
                            'shape': 'ellipse',
                            'text-outline-color': '#fff',
                            'text-outline-width': 3
                        }
                    },
                    {
                        selector: 'node.configuracion',
                        style: {
                            'background-color': '#8b5cf6',
                            'label': 'data(label)',
                            'color': '#000',
                            'font-size': '18px',
                            'font-weight': '900',
                            'width': '120px',
                            'height': '120px',
                            'text-valign': 'center',
                            'text-halign': 'center',
                            'text-wrap': 'wrap',
                            'text-max-width': '140px',
                            'border-width': 4,
                            'border-color': '#6d28d9',
                            'shape': 'ellipse',
                            'text-outline-color': '#fff',
                            'text-outline-width': 3
                        }
                    },
                    {
                        selector: 'node.otro',
                        style: {
                            'background-color': '#6b7280',
                            'label': 'data(label)',
                            'color': '#000',
                            'font-size': '18px',
                            'font-weight': '900',
                            'width': '120px',
                            'height': '120px',
                            'text-valign': 'center',
                            'text-halign': 'center',
                            'text-wrap': 'wrap',
                            'text-max-width': '140px',
                            'border-width': 4,
                            'border-color': '#374151',
                            'shape': 'ellipse',
                            'text-outline-color': '#fff',
                            'text-outline-width': 3
                        }
                    },
                    {
                        selector: 'edge',
                        style: {
                            'width': 2,
                            'line-color': '#64748b',
                            'target-arrow-color': '#64748b',
                            'target-arrow-shape': 'triangle',
                            'curve-style': 'unbundled-bezier',
                            'control-point-distances': [40, -40],
                            'control-point-weights': [0.25, 0.75],
                            'label': 'data(label)',
                            'font-size': '14px',
                            'font-weight': '900',
                            'color': '#000',
                            'text-background-color': '#fff',
                            'text-background-opacity': 1,
                            'text-background-padding': '6px',
                            'text-border-width': 2,
                            'text-border-color': '#cbd5e1',
                            'text-border-opacity': 1
                        }
                    }
                ],
                layout: {
                    name: 'cose',
                    animate: true,
                    animationDuration: 1500,
                    animationEasing: 'ease-out',
                    nodeDimensionsIncludeLabels: true,
                    fit: true,
                    padding: 80,
                    nodeRepulsion: 8000,
                    nodeOverlap: 80,
                    idealEdgeLength: 200,
                    edgeElasticity: 100,
                    nestingFactor: 1.2,
                    gravity: 0.5,
                    numIter: 1000,
                    initialTemp: 200,
                    coolingFactor: 0.95,
                    minTemp: 1.0
                }
            });
        }

        function exportarPNG() {
            // Implementar exportación según la vista activa
            const sankeyVisible = document.getElementById('sankey-container').style.display !== 'none';

            if (sankeyVisible) {
                const svg = document.querySelector('#sankey-container svg');
                const serializer = new XMLSerializer();
                const svgString = serializer.serializeToString(svg);
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();

                img.onload = function() {
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0);
                    const link = document.createElement('a');
                    link.download = 'grafo-trazabilidad-{{ $proyecto->codigo }}.png';
                    link.href = canvas.toDataURL();
                    link.click();
                };

                img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgString)));
            } else {
                alert('Exportación solo disponible en vista Sankey');
            }
        }

        // Event Listeners
        document.getElementById('btnSankey')?.addEventListener('click', function() {
            // Actualizar botones activos
            document.getElementById('btnSankey').classList.remove('btn-outline');
            document.getElementById('btnSankey').classList.add('btn-primary');
            document.getElementById('btnJerarquico').classList.remove('btn-primary');
            document.getElementById('btnJerarquico').classList.add('btn-outline', 'btn-primary');
            document.getElementById('btnCircular').classList.remove('btn-primary');
            document.getElementById('btnCircular').classList.add('btn-outline', 'btn-primary');

            drawSankey();
        });

        document.getElementById('btnJerarquico')?.addEventListener('click', function() {
            document.getElementById('btnSankey').classList.remove('btn-primary');
            document.getElementById('btnSankey').classList.add('btn-outline', 'btn-primary');
            document.getElementById('btnJerarquico').classList.remove('btn-outline');
            document.getElementById('btnJerarquico').classList.add('btn-primary');
            document.getElementById('btnCircular').classList.remove('btn-primary');
            document.getElementById('btnCircular').classList.add('btn-outline', 'btn-primary');

            drawGraph('jerarquico');
        });

        document.getElementById('btnCircular')?.addEventListener('click', function() {
            document.getElementById('btnSankey').classList.remove('btn-primary');
            document.getElementById('btnSankey').classList.add('btn-outline', 'btn-primary');
            document.getElementById('btnJerarquico').classList.remove('btn-primary');
            document.getElementById('btnJerarquico').classList.add('btn-outline', 'btn-primary');
            document.getElementById('btnCircular').classList.remove('btn-outline');
            document.getElementById('btnCircular').classList.add('btn-primary');

            drawCircular();
        });

        document.getElementById('btnExportar')?.addEventListener('click', exportarPNG);
    </script>
    @endpush
</x-app-layout>
