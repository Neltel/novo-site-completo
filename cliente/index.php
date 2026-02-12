<?php
/**
 * Portal do Cliente - Página Inicial
 * Sistema NM Refrigeração
 * 
 * Função: Página pública com informações da empresa e agendamento online
 * Recursos: Catálogo de serviços, agendamento, calculadora térmica, contato
 * Uso: Página inicial do site (/)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';

$db = new Database();
$pdo = $db->getConnection();

// Busca configurações da empresa
$stmt = $pdo->query("SELECT * FROM configuracoes WHERE grupo = 'empresa'");
$configs_raw = $stmt->fetchAll();
$configs = [];
foreach ($configs_raw as $config) {
    $configs[$config['chave']] = $config['valor'];
}

// Busca serviços ativos e visíveis
$stmt = $pdo->query("
    SELECT * FROM servicos 
    WHERE ativo = 1 
    ORDER BY nome ASC
");
$servicos = $stmt->fetchAll();

// Busca posts do Instagram (simulado - implementar API real)
$instagram_posts = [
    ['img' => '/assets/img/instagram/1.jpg', 'likes' => 245, 'comentarios' => 15],
    ['img' => '/assets/img/instagram/2.jpg', 'likes' => 320, 'comentarios' => 28],
    ['img' => '/assets/img/instagram/3.jpg', 'likes' => 189, 'comentarios' => 12],
    ['img' => '/assets/img/instagram/4.jpg', 'likes' => 412, 'comentarios' => 35],
    ['img' => '/assets/img/instagram/5.jpg', 'likes' => 278, 'comentarios' => 19],
    ['img' => '/assets/img/instagram/6.jpg', 'likes' => 356, 'comentarios' => 24],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($configs['empresa_nome'] ?? 'NM Refrigeração') ?> - Serviços especializados em ar condicionado. Instalação, manutenção e reparo.">
    <meta name="keywords" content="ar condicionado, refrigeração, manutenção, instalação, assistência técnica">
    <title><?= htmlspecialchars($configs['empresa_nome'] ?? 'NM Refrigeração') ?> - Serviços de Ar Condicionado</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/cliente.css">
    
    <!-- Favicons -->
    <link rel="icon" href="/assets/img/favicon.ico">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?= htmlspecialchars($configs['empresa_nome'] ?? 'NM Refrigeração') ?>">
    <meta property="og:description" content="Serviços especializados em ar condicionado">
    <meta property="og:type" content="website">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="/" class="navbar-logo">
                <img src="/assets/img/logo.png" alt="<?= htmlspecialchars($configs['empresa_nome'] ?? 'NM Refrigeração') ?>" onerror="this.style.display='none'">
                <span><?= htmlspecialchars($configs['empresa_nome'] ?? 'NM Refrigeração') ?></span>
            </a>
            
            <button class="navbar-toggle" onclick="toggleMenu()">☰</button>
            
            <ul class="navbar-menu" id="navbar-menu">
                <li><a href="#inicio" class="navbar-link ativo">Início</a></li>
                <li><a href="#servicos" class="navbar-link">Serviços</a></li>
                <li><a href="#calculadora" class="navbar-link">Calculadora</a></li>
                <li><a href="#instagram" class="navbar-link">Instagram</a></li>
                <li><a href="#contato" class="navbar-link">Contato</a></li>
                <li><a href="#" onclick="abrirModalAgendamento(); return false;" class="btn-agendar">Agendar Serviço</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="inicio" class="hero">
        <div class="hero-container">
            <h1>Especialistas em Ar Condicionado</h1>
            <p>Instalação, manutenção preventiva e corretiva com garantia e qualidade. Atendemos residências e empresas em toda a região.</p>
            <div class="hero-buttons">
                <button onclick="abrirModalAgendamento()" class="btn btn-primario">
                    📅 Agendar Atendimento
                </button>
                <a href="#servicos" class="btn btn-outline">
                    🔧 Ver Serviços
                </a>
            </div>
        </div>
    </section>

    <!-- Benefícios -->
    <section class="section">
        <div class="container">
            <h2 class="section-titulo">Por que nos escolher?</h2>
            <p class="section-subtitulo">Experiência, qualidade e compromisso com nossos clientes</p>
            
            <div class="features-grid">
                <div class="feature fade-in">
                    <div class="feature-icone">🛡️</div>
                    <h3>Garantia Estendida</h3>
                    <p>Todos os serviços com garantia e suporte técnico especializado</p>
                </div>
                
                <div class="feature fade-in">
                    <div class="feature-icone">⚡</div>
                    <h3>Atendimento Rápido</h3>
                    <p>Equipe pronta para atender emergências e agendamentos</p>
                </div>
                
                <div class="feature fade-in">
                    <div class="feature-icone">💰</div>
                    <h3>Preços Justos</h3>
                    <p>Orçamentos transparentes sem custos ocultos</p>
                </div>
                
                <div class="feature fade-in">
                    <div class="feature-icone">👨‍🔧</div>
                    <h3>Técnicos Certificados</h3>
                    <p>Profissionais qualificados e treinados continuamente</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Serviços -->
    <section id="servicos" class="section" style="background-color: var(--cor-fundo-alt);">
        <div class="container">
            <h2 class="section-titulo">Nossos Serviços</h2>
            <p class="section-subtitulo">Soluções completas para seu conforto térmico</p>
            
            <div class="servicos-grid">
                <?php foreach ($servicos as $servico): ?>
                <div class="servico-card fade-in" onclick="selecionarServico(<?= $servico['id'] ?>)">
                    <div class="servico-icone">🔧</div>
                    <h3 class="servico-titulo"><?= htmlspecialchars($servico['nome']) ?></h3>
                    <p class="servico-descricao"><?= htmlspecialchars(substr($servico['descricao'] ?? '', 0, 100)) ?>...</p>
                    
                    <?php if ($servico['preco_base'] > 0): ?>
                    <div class="servico-preco">
                        A partir de R$ <?= number_format($servico['preco_base'], 2, ',', '.') ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($servico['tempo_estimado']): ?>
                    <ul class="servico-detalhes">
                        <li>Tempo estimado: <?= $servico['tempo_estimado'] ?> minutos</li>
                        <?php if ($servico['materiais_inclusos']): ?>
                        <li>Materiais inclusos</li>
                        <?php endif; ?>
                        <li>Garantia do serviço</li>
                    </ul>
                    <?php endif; ?>
                    
                    <button class="btn btn-primario w-full" onclick="agendarServico(<?= $servico['id'] ?>)">
                        Agendar Este Serviço
                    </button>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($servicos)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: var(--cor-texto-claro);">
                    <p>Nenhum serviço disponível no momento. Entre em contato para mais informações.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Calculadora de Carga Térmica -->
    <section id="calculadora" class="section">
        <div class="container">
            <h2 class="section-titulo">Calculadora de Carga Térmica</h2>
            <p class="section-subtitulo">Descubra qual a potência ideal de ar condicionado para seu ambiente</p>
            
            <div class="calculadora">
                <form class="calc-form" id="form-calculadora" onsubmit="calcularCargaTermica(event)">
                    <div class="form-group">
                        <label class="form-label">Área do Ambiente (m²)</label>
                        <input type="number" id="area" class="form-input" required min="1" step="0.1" 
                               placeholder="Ex: 20">
                        <small style="color: var(--cor-texto-claro); display: block; margin-top: 0.25rem;">
                            Comprimento × Largura em metros
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Tipo de Ambiente</label>
                        <select id="tipo-ambiente" class="form-select" required>
                            <option value="">Selecione...</option>
                            <option value="residencial">Residencial (Quarto/Sala)</option>
                            <option value="comercial">Comercial (Escritório)</option>
                            <option value="cozinha">Cozinha</option>
                            <option value="servidor">Sala de Servidores</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Número de Pessoas</label>
                        <input type="number" id="pessoas" class="form-input" required min="0" value="2"
                               placeholder="Ex: 2">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Incidência Solar</label>
                        <select id="solar" class="form-select" required>
                            <option value="baixa">Baixa (ambiente interno/sombreado)</option>
                            <option value="media" selected>Média (janela lateral)</option>
                            <option value="alta">Alta (janela com sol direto)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Equipamentos Eletrônicos</label>
                        <select id="equipamentos" class="form-select" required>
                            <option value="poucos">Poucos (TV, 1-2 computadores)</option>
                            <option value="medios" selected>Médio (vários equipamentos)</option>
                            <option value="muitos">Muitos (sala de informática)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primario" style="width: 100%;">
                        🧮 Calcular Potência Necessária
                    </button>
                </form>
                
                <div id="resultado-calc" class="resultado-calc">
                    <div class="resultado-valor" id="resultado-valor"></div>
                    <div class="resultado-desc" id="resultado-desc"></div>
                    <button onclick="abrirModalAgendamento()" class="btn btn-outline mt-4" style="width: 100%;">
                        Solicitar Orçamento
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Instagram -->
    <section id="instagram" class="section" style="background-color: var(--cor-fundo-alt);">
        <div class="container">
            <h2 class="section-titulo">Siga-nos no Instagram</h2>
            <p class="section-subtitulo">Confira nossos trabalhos e novidades</p>
            
            <div class="instagram-grid">
                <?php foreach ($instagram_posts as $index => $post): ?>
                <div class="instagram-post">
                    <img src="<?= $post['img'] ?>" alt="Instagram Post <?= $index + 1 ?>" 
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 400%22%3E%3Crect fill=%22%23e5e7eb%22 width=%22400%22 height=%22400%22/%3E%3Ctext x=%22200%22 y=%22200%22 font-size=%2280%22 fill=%22%239ca3af%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3E📷%3C/text%3E%3C/svg%3E'">
                    <div class="instagram-overlay">
                        <div>
                            <span>❤️ <?= $post['likes'] ?></span>
                            <span style="margin-left: 1rem;">💬 <?= $post['comentarios'] ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align: center; margin-top: var(--espacamento-xl);">
                <a href="https://instagram.com/nmrefrigeracao" target="_blank" class="btn btn-primario">
                    📸 Ver Mais no Instagram
                </a>
            </div>
        </div>
    </section>

    <!-- Contato -->
    <section id="contato" class="section contato-section">
        <div class="container">
            <h2 class="section-titulo">Entre em Contato</h2>
            <p class="section-subtitulo">Estamos prontos para atender você</p>
            
            <div class="contato-grid">
                <div class="contato-item">
                    <div class="contato-icone">📞</div>
                    <h3>Telefone</h3>
                    <p>
                        <a href="tel:<?= preg_replace('/[^0-9]/', '', $configs['empresa_telefone'] ?? '') ?>">
                            <?= htmlspecialchars($configs['empresa_telefone'] ?? '(11) 99999-9999') ?>
                        </a>
                    </p>
                </div>
                
                <div class="contato-item">
                    <div class="contato-icone">💬</div>
                    <h3>WhatsApp</h3>
                    <p>
                        <a href="https://wa.me/55<?= preg_replace('/[^0-9]/', '', $configs['empresa_telefone'] ?? '') ?>" target="_blank">
                            Enviar mensagem
                        </a>
                    </p>
                </div>
                
                <div class="contato-item">
                    <div class="contato-icone">✉️</div>
                    <h3>Email</h3>
                    <p>
                        <a href="mailto:<?= htmlspecialchars($configs['empresa_email'] ?? 'contato@nmrefrigeracao.business') ?>">
                            <?= htmlspecialchars($configs['empresa_email'] ?? 'contato@nmrefrigeracao.business') ?>
                        </a>
                    </p>
                </div>
                
                <div class="contato-item">
                    <div class="contato-icone">📍</div>
                    <h3>Endereço</h3>
                    <p>
                        <?= htmlspecialchars($configs['empresa_endereco'] ?? 'Rua Exemplo, 123') ?><br>
                        <?= htmlspecialchars($configs['empresa_cidade'] ?? 'São Paulo') ?> - <?= htmlspecialchars($configs['empresa_estado'] ?? 'SP') ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($configs['empresa_nome'] ?? 'NM Refrigeração') ?>. Todos os direitos reservados.</p>
            <p style="margin-top: 0.5rem; opacity: 0.7;">
                <a href="/admin" style="color: white;">Área do Administrador</a>
            </p>
        </div>
    </footer>

    <!-- Modal de Agendamento -->
    <div id="modal-agendamento" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-titulo">📅 Agendar Serviço</h3>
                <button class="modal-fechar" onclick="fecharModalAgendamento()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-agendamento" onsubmit="enviarAgendamento(event)">
                    <div class="form-group">
                        <label class="form-label">Serviço *</label>
                        <select name="servico_id" class="form-select" required>
                            <option value="">Selecione um serviço...</option>
                            <?php foreach ($servicos as $srv): ?>
                            <option value="<?= $srv['id'] ?>" data-tempo="<?= $srv['tempo_estimado'] ?>">
                                <?= htmlspecialchars($srv['nome']) ?>
                                <?php if ($srv['preco_base'] > 0): ?>
                                - R$ <?= number_format($srv['preco_base'], 2, ',', '.') ?>
                                <?php endif; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Seu Nome *</label>
                        <input type="text" name="nome" class="form-input" required placeholder="Nome completo">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">WhatsApp *</label>
                        <input type="tel" name="telefone" class="form-input" required placeholder="(00) 00000-0000">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" placeholder="seu@email.com">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Endereço do Serviço *</label>
                        <input type="text" name="endereco" class="form-input" required 
                               placeholder="Rua, número, bairro, cidade">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Data Preferida *</label>
                        <input type="date" name="data" class="form-input" required 
                               min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Horário Preferido *</label>
                        <select name="horario" class="form-select" required>
                            <option value="">Selecione...</option>
                            <option value="08:00">08:00</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="13:00">13:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:00">15:00</option>
                            <option value="16:00">16:00</option>
                            <option value="17:00">17:00</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Observações</label>
                        <textarea name="observacoes" class="form-input" rows="3" 
                                  placeholder="Informações adicionais sobre o serviço"></textarea>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" onclick="fecharModalAgendamento()" class="btn btn-outline">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primario">
                            📅 Confirmar Agendamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Toggle menu mobile
        function toggleMenu() {
            document.getElementById('navbar-menu').classList.toggle('ativo');
        }

        // Fecha menu ao clicar em link
        document.querySelectorAll('.navbar-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    document.getElementById('navbar-menu').classList.remove('ativo');
                }
            });
        });

        // Marca link ativo baseado no scroll
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('section[id]');
            const scrollPos = window.scrollY + 100;
            
            sections.forEach(section => {
                const top = section.offsetTop;
                const height = section.offsetHeight;
                const id = section.getAttribute('id');
                
                if (scrollPos >= top && scrollPos < top + height) {
                    document.querySelectorAll('.navbar-link').forEach(link => {
                        link.classList.remove('ativo');
                        if (link.getAttribute('href') === `#${id}`) {
                            link.classList.add('ativo');
                        }
                    });
                }
            });
        });

        // Modal de agendamento
        function abrirModalAgendamento() {
            document.getElementById('modal-agendamento').classList.add('ativo');
            document.body.style.overflow = 'hidden';
        }

        function fecharModalAgendamento() {
            document.getElementById('modal-agendamento').classList.remove('ativo');
            document.body.style.overflow = '';
        }

        function agendarServico(servicoId) {
            abrirModalAgendamento();
            document.querySelector('select[name="servico_id"]').value = servicoId;
        }

        function selecionarServico(servicoId) {
            // Scroll para formulário de agendamento
            abrirModalAgendamento();
            document.querySelector('select[name="servico_id"]').value = servicoId;
        }

        // Enviar agendamento
        async function enviarAgendamento(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch('/api/agendamentos.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        ...data,
                        status: 'pendente',
                        origem: 'site'
                    })
                });
                
                const result = await response.json();
                
                if (result.sucesso) {
                    alert('✅ Agendamento solicitado com sucesso!\n\nEntraremos em contato em breve para confirmar.');
                    fecharModalAgendamento();
                    form.reset();
                    
                    // Envia mensagem via WhatsApp
                    const whatsapp = '<?= preg_replace('/[^0-9]/', '', $configs['empresa_telefone'] ?? '') ?>';
                    const mensagem = `Olá! Gostaria de agendar: ${data.nome} - ${document.querySelector(`option[value="${data.servico_id}"]`).text}`;
                    window.open(`https://wa.me/55${whatsapp}?text=${encodeURIComponent(mensagem)}`, '_blank');
                } else {
                    alert('❌ Erro ao agendar: ' + (result.mensagem || 'Tente novamente'));
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('❌ Erro ao enviar agendamento. Tente novamente ou entre em contato por telefone.');
            }
        }

        // Calculadora de carga térmica
        function calcularCargaTermica(event) {
            event.preventDefault();
            
            const area = parseFloat(document.getElementById('area').value);
            const tipoAmbiente = document.getElementById('tipo-ambiente').value;
            const pessoas = parseInt(document.getElementById('pessoas').value);
            const solar = document.getElementById('solar').value;
            const equipamentos = document.getElementById('equipamentos').value;
            
            // Fator base por tipo de ambiente
            let fatorBase = 600; // BTUs por m²
            
            if (tipoAmbiente === 'comercial') fatorBase = 650;
            if (tipoAmbiente === 'cozinha') fatorBase = 800;
            if (tipoAmbiente === 'servidor') fatorBase = 900;
            
            // Cálculo base
            let btus = area * fatorBase;
            
            // Adiciona pessoas (600 BTUs por pessoa)
            btus += pessoas * 600;
            
            // Adiciona fator solar
            if (solar === 'media') btus += area * 100;
            if (solar === 'alta') btus += area * 200;
            
            // Adiciona equipamentos
            if (equipamentos === 'medios') btus += area * 100;
            if (equipamentos === 'muitos') btus += area * 200;
            
            // Arredonda para o padrão comercial mais próximo
            const padroes = [7000, 9000, 12000, 18000, 22000, 24000, 30000, 36000, 48000, 60000];
            const btuRecomendado = padroes.find(p => p >= btus) || btus;
            
            // Exibe resultado
            document.getElementById('resultado-valor').textContent = `${btuRecomendado.toLocaleString()} BTUs`;
            document.getElementById('resultado-desc').textContent = 
                `Recomendamos um aparelho de ${btuRecomendado.toLocaleString()} BTUs para seu ambiente de ${area}m²`;
            document.getElementById('resultado-calc').classList.add('visivel');
            
            // Scroll para resultado
            document.getElementById('resultado-calc').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // Máscara de telefone
        document.querySelector('input[name="telefone"]')?.addEventListener('input', (e) => {
            let valor = e.target.value.replace(/\D/g, '');
            if (valor.length <= 11) {
                valor = valor.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
                e.target.value = valor;
            }
        });
    </script>
</body>
</html>
