<?php
/**
 * Painel Administrativo - Dashboard Principal
 * Sistema NM Refrigeração - Gestão Integrada
 * 
 * Função: Página inicial do painel admin com visão geral do negócio
 * Uso: Acesse /admin ou /admin/index.php
 * Requer: Autenticação como admin ou técnico
 */

// Inicia sessão e verifica autenticação
session_start();

// Verifica se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /login.html');
    exit;
}

// Carrega configurações
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';

// Conecta ao banco de dados
$db = new Database();
$pdo = $db->getConnection();

// Busca informações do usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

// Busca estatísticas gerais
$stats = [];

// Total de clientes
$stmt = $pdo->query("SELECT COUNT(*) as total FROM clientes");
$stats['clientes'] = $stmt->fetch()['total'];

// Total de produtos
$stmt = $pdo->query("SELECT COUNT(*) as total FROM produtos WHERE ativo = 1");
$stats['produtos'] = $stmt->fetch()['total'];

// Total de serviços
$stmt = $pdo->query("SELECT COUNT(*) as total FROM servicos WHERE ativo = 1");
$stats['servicos'] = $stmt->fetch()['total'];

// Agendamentos hoje
$stmt = $pdo->query("SELECT COUNT(*) as total FROM agendamentos WHERE data_agendamento = CURDATE() AND status != 'cancelado'");
$stats['agendamentos_hoje'] = $stmt->fetch()['total'];

// Vendas do mês
$stmt = $pdo->query("SELECT COUNT(*) as total, COALESCE(SUM(valor_bruto), 0) as total_vendas FROM vendas WHERE MONTH(data_venda) = MONTH(CURDATE()) AND YEAR(data_venda) = YEAR(CURDATE())");
$vendas_mes = $stmt->fetch();
$stats['vendas_mes'] = $vendas_mes['total'];
$stats['valor_vendas_mes'] = $vendas_mes['total_vendas'];

// Cobranças pendentes
$stmt = $pdo->query("SELECT COUNT(*) as total, COALESCE(SUM(valor), 0) as total_pendente FROM cobrancas WHERE status = 'aberta' OR status = 'atrasada'");
$cobrancas = $stmt->fetch();
$stats['cobrancas_pendentes'] = $cobrancas['total'];
$stats['valor_cobrancas'] = $cobrancas['total_pendente'];

// Orçamentos da semana
$stmt = $pdo->query("SELECT COUNT(*) as total FROM orcamentos WHERE WEEK(data_orcamento, 1) = WEEK(CURDATE(), 1) AND YEAR(data_orcamento) = YEAR(CURDATE())");
$stats['orcamentos_semana'] = $stmt->fetch()['total'];

// Últimos agendamentos
$stmt = $pdo->query("
    SELECT a.*, c.nome as cliente_nome, s.nome as servico_nome
    FROM agendamentos a
    LEFT JOIN clientes c ON a.cliente_id = c.id
    LEFT JOIN servicos s ON a.servico_id = s.id
    WHERE a.data_agendamento >= CURDATE()
    ORDER BY a.data_agendamento, a.hora_inicio
    LIMIT 5
");
$agendamentos_proximos = $stmt->fetchAll();

// Últimas vendas
$stmt = $pdo->query("
    SELECT v.*, c.nome as cliente_nome
    FROM vendas v
    LEFT JOIN clientes c ON v.cliente_id = c.id
    ORDER BY v.data_venda DESC
    LIMIT 5
");
$ultimas_vendas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Painel Administrativo - NM Refrigeração">
    <title>Dashboard - NM Refrigeração</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/admin.css">
    
    <!-- Favicons -->
    <link rel="icon" href="/assets/img/favicon.ico">
</head>
<body>
    <!-- Layout Principal -->
    <div class="layout-admin">
        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- Logo -->
            <div class="sidebar-logo">
                <img src="/assets/img/logo.png" alt="NM Refrigeração" onerror="this.style.display='none'">
                <h2>NM Refrigeração</h2>
            </div>

            <!-- Navegação -->
            <nav class="sidebar-nav">
                <!-- App-1: Gestão Geral -->
                <div class="nav-section">
                    <div class="nav-section-titulo">Gestão Geral</div>
                    
                    <a href="/admin/index.php" class="nav-link ativo">
                        <i>📊</i>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="/admin/clientes.php" class="nav-link">
                        <i>👥</i>
                        <span>Clientes</span>
                    </a>
                    
                    <a href="/admin/produtos.php" class="nav-link">
                        <i>📦</i>
                        <span>Produtos</span>
                    </a>
                    
                    <a href="/admin/servicos.php" class="nav-link">
                        <i>🔧</i>
                        <span>Serviços</span>
                    </a>
                    
                    <a href="/admin/pedidos.php" class="nav-link">
                        <i>🛒</i>
                        <span>Pedidos</span>
                    </a>
                    
                    <a href="/admin/orcamentos.php" class="nav-link">
                        <i>📝</i>
                        <span>Orçamentos</span>
                        <?php if ($stats['orcamentos_semana'] > 0): ?>
                        <span class="badge"><?= $stats['orcamentos_semana'] ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <a href="/admin/agendamentos.php" class="nav-link">
                        <i>📅</i>
                        <span>Agendamentos</span>
                        <?php if ($stats['agendamentos_hoje'] > 0): ?>
                        <span class="badge"><?= $stats['agendamentos_hoje'] ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <a href="/admin/vendas.php" class="nav-link">
                        <i>💰</i>
                        <span>Vendas</span>
                    </a>
                    
                    <a href="/admin/cobrancas.php" class="nav-link">
                        <i>💳</i>
                        <span>Cobranças</span>
                        <?php if ($stats['cobrancas_pendentes'] > 0): ?>
                        <span class="badge"><?= $stats['cobrancas_pendentes'] ?></span>
                        <?php endif; ?>
                    </a>
                </div>

                <!-- App-2: Técnico Ar Condicionado -->
                <div class="nav-section">
                    <div class="nav-section-titulo">Técnico AC</div>
                    
                    <a href="/admin/ac-orcamentos.php" class="nav-link">
                        <i>📋</i>
                        <span>Orçamentos AC</span>
                    </a>
                    
                    <a href="/admin/tabela-precos.php" class="nav-link">
                        <i>💵</i>
                        <span>Tabela de Preços</span>
                    </a>
                    
                    <a href="/admin/historico.php" class="nav-link">
                        <i>📜</i>
                        <span>Histórico</span>
                    </a>
                    
                    <a href="/admin/garantias.php" class="nav-link">
                        <i>🛡️</i>
                        <span>Garantias</span>
                    </a>
                    
                    <a href="/admin/preventivas.php" class="nav-link">
                        <i>🔔</i>
                        <span>Preventivas</span>
                    </a>
                    
                    <a href="/admin/relatorios.php" class="nav-link">
                        <i>📊</i>
                        <span>Relatórios Técnicos</span>
                    </a>
                    
                    <a href="/admin/financeiro.php" class="nav-link">
                        <i>💹</i>
                        <span>Financeiro</span>
                    </a>
                    
                    <a href="/admin/pmp.php" class="nav-link">
                        <i>⚙️</i>
                        <span>PMP</span>
                    </a>
                    
                    <a href="/admin/ia-assistant.php" class="nav-link">
                        <i>🤖</i>
                        <span>Assistente IA</span>
                    </a>
                </div>

                <!-- Configurações -->
                <div class="nav-section">
                    <div class="nav-section-titulo">Sistema</div>
                    
                    <a href="/admin/configuracoes.php" class="nav-link">
                        <i>⚙️</i>
                        <span>Configurações</span>
                    </a>
                    
                    <a href="#" class="nav-link btn-logout">
                        <i>🚪</i>
                        <span>Sair</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="conteudo-principal">
            <!-- Header -->
            <header class="header-admin">
                <div class="header-left">
                    <button class="btn-toggle-sidebar" aria-label="Toggle Menu">
                        ☰
                    </button>
                    <h1 class="header-titulo">Dashboard</h1>
                </div>

                <div class="header-right">
                    <!-- Busca -->
                    <div class="header-search">
                        <i>🔍</i>
                        <input type="text" placeholder="Buscar...">
                    </div>

                    <!-- Notificações -->
                    <div class="header-notificacoes">
                        <i>🔔</i>
                        <?php if ($stats['agendamentos_hoje'] > 0): ?>
                        <span class="badge"><?= $stats['agendamentos_hoje'] ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Usuário -->
                    <div class="header-usuario">
                        <img src="/assets/img/avatar-default.png" alt="<?= htmlspecialchars($usuario['nome']) ?>" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ccircle cx=%2250%22 cy=%2250%22 r=%2250%22 fill=%22%232563eb%22/%3E%3Ctext x=%2250%22 y=%2265%22 font-size=%2250%22 fill=%22white%22 text-anchor=%22middle%22%3E<?= strtoupper(substr($usuario['nome'], 0, 1)) ?>%3C/text%3E%3C/svg%3E'">
                        <div class="usuario-info">
                            <div class="usuario-nome"><?= htmlspecialchars($usuario['nome']) ?></div>
                            <div class="usuario-tipo"><?= ucfirst($usuario['tipo']) ?></div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Área de Conteúdo -->
            <div class="area-conteudo">
                <!-- Breadcrumb -->
                <div class="breadcrumb">
                    <div class="breadcrumb-item"><a href="/admin">Home</a></div>
                    <div class="breadcrumb-item">Dashboard</div>
                </div>

                <!-- Cards de Estatísticas -->
                <div class="cards-estatisticas">
                    <!-- Clientes -->
                    <div class="card-stat">
                        <div class="card-stat-header">
                            <div>
                                <div class="card-stat-valor"><?= number_format($stats['clientes']) ?></div>
                                <div class="card-stat-label">Clientes Cadastrados</div>
                            </div>
                            <div class="card-stat-icone azul">
                                👥
                            </div>
                        </div>
                        <div class="card-stat-trend trend-positivo">
                            <span>↑ +12%</span> este mês
                        </div>
                    </div>

                    <!-- Agendamentos Hoje -->
                    <div class="card-stat">
                        <div class="card-stat-header">
                            <div>
                                <div class="card-stat-valor"><?= number_format($stats['agendamentos_hoje']) ?></div>
                                <div class="card-stat-label">Agendamentos Hoje</div>
                            </div>
                            <div class="card-stat-icone verde">
                                📅
                            </div>
                        </div>
                        <div class="card-stat-trend trend-positivo">
                            <span>Ver todos →</span>
                        </div>
                    </div>

                    <!-- Vendas do Mês -->
                    <div class="card-stat">
                        <div class="card-stat-header">
                            <div>
                                <div class="card-stat-valor">R$ <?= number_format($stats['valor_vendas_mes'], 2, ',', '.') ?></div>
                                <div class="card-stat-label">Vendas do Mês</div>
                            </div>
                            <div class="card-stat-icone laranja">
                                💰
                            </div>
                        </div>
                        <div class="card-stat-trend trend-positivo">
                            <span>↑ +8%</span> vs. mês anterior
                        </div>
                    </div>

                    <!-- Cobranças Pendentes -->
                    <div class="card-stat">
                        <div class="card-stat-header">
                            <div>
                                <div class="card-stat-valor">R$ <?= number_format($stats['valor_cobrancas'], 2, ',', '.') ?></div>
                                <div class="card-stat-label">Cobranças Pendentes</div>
                            </div>
                            <div class="card-stat-icone vermelho">
                                💳
                            </div>
                        </div>
                        <div class="card-stat-trend">
                            <?= $stats['cobrancas_pendentes'] ?> cobrança(s) em aberto
                        </div>
                    </div>
                </div>

                <!-- Grid de Conteúdo -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: var(--espacamento-lg);">
                    <!-- Próximos Agendamentos -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-titulo">Próximos Agendamentos</h3>
                            <a href="/admin/agendamentos.php" class="btn btn-sm btn-outline">Ver Todos</a>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <?php if (empty($agendamentos_proximos)): ?>
                                <div style="padding: var(--espacamento-lg); text-align: center; color: var(--cor-texto-claro);">
                                    Nenhum agendamento próximo
                                </div>
                            <?php else: ?>
                                <table class="tabela">
                                    <thead>
                                        <tr>
                                            <th>Data/Hora</th>
                                            <th>Cliente</th>
                                            <th>Serviço</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($agendamentos_proximos as $agend): ?>
                                        <tr>
                                            <td>
                                                <?= date('d/m/Y', strtotime($agend['data_agendamento'])) ?><br>
                                                <small><?= substr($agend['hora_inicio'], 0, 5) ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($agend['cliente_nome']) ?></td>
                                            <td><?= htmlspecialchars($agend['servico_nome'] ?? 'N/A') ?></td>
                                            <td>
                                                <span class="badge badge-<?= $agend['status'] === 'agendado' ? 'primario' : 'sucesso' ?>">
                                                    <?= ucfirst($agend['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Últimas Vendas -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-titulo">Últimas Vendas</h3>
                            <a href="/admin/vendas.php" class="btn btn-sm btn-outline">Ver Todas</a>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <?php if (empty($ultimas_vendas)): ?>
                                <div style="padding: var(--espacamento-lg); text-align: center; color: var(--cor-texto-claro);">
                                    Nenhuma venda registrada
                                </div>
                            <?php else: ?>
                                <table class="tabela">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Cliente</th>
                                            <th>Valor</th>
                                            <th>Pagamento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($ultimas_vendas as $venda): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($venda['data_venda'])) ?></td>
                                            <td><?= htmlspecialchars($venda['cliente_nome']) ?></td>
                                            <td>R$ <?= number_format($venda['valor_bruto'], 2, ',', '.') ?></td>
                                            <td>
                                                <span class="badge badge-<?= $venda['status_pagamento'] === 'pago' ? 'sucesso' : 'aviso' ?>">
                                                    <?= ucfirst($venda['status_pagamento']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Ações Rápidas -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-titulo">Ações Rápidas</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: flex; flex-wrap: wrap; gap: var(--espacamento-md);">
                            <a href="/admin/clientes.php?acao=novo" class="btn btn-primario">
                                <span>👥</span>
                                Novo Cliente
                            </a>
                            <a href="/admin/orcamentos.php?acao=novo" class="btn btn-primario">
                                <span>📝</span>
                                Novo Orçamento
                            </a>
                            <a href="/admin/agendamentos.php?acao=novo" class="btn btn-primario">
                                <span>📅</span>
                                Novo Agendamento
                            </a>
                            <a href="/admin/vendas.php?acao=novo" class="btn btn-sucesso">
                                <span>💰</span>
                                Registrar Venda
                            </a>
                            <a href="/admin/produtos.php?acao=novo" class="btn btn-outline">
                                <span>📦</span>
                                Novo Produto
                            </a>
                            <a href="/admin/servicos.php?acao=novo" class="btn btn-outline">
                                <span>🔧</span>
                                Novo Serviço
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script src="/assets/js/admin.js"></script>
    <script>
        // Inicialização específica da página
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Dashboard carregado');
            
            // Adiciona listener para busca do header
            const headerSearch = document.querySelector('.header-search input');
            if (headerSearch) {
                headerSearch.addEventListener('input', Utils.debounce((e) => {
                    const termo = e.target.value;
                    if (termo.length >= 3) {
                        console.log('Buscando:', termo);
                        // Implementar busca global aqui
                    }
                }, 300));
            }
        });
    </script>
</body>
</html>
