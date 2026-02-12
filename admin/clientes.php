<?php
/**
 * Módulo de Gestão de Clientes
 * Sistema NM Refrigeração
 * 
 * Função: CRUD completo de clientes (Cadastrar, Pesquisar, Editar, Excluir)
 * Recursos: Importar agenda, anotações, anexos, histórico
 * Uso: /admin/clientes.php
 */

session_start();

// Verifica autenticação
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /login.html');
    exit;
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';

$db = new Database();
$pdo = $db->getConnection();

// Busca informações do usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

// Busca total de clientes para estatísticas
$stmt = $pdo->query("SELECT COUNT(*) as total FROM clientes");
$total_clientes = $stmt->fetch()['total'];

// Determina ação (novo, editar, listar)
$acao = $_GET['acao'] ?? 'listar';
$cliente_id = $_GET['id'] ?? null;
$cliente = null;

// Se editando, busca dados do cliente
if ($acao === 'editar' && $cliente_id) {
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$cliente_id]);
    $cliente = $stmt->fetch();
    
    if (!$cliente) {
        $acao = 'listar';
        $erro = 'Cliente não encontrado';
    }
}

// Busca clientes para listagem
$clientes = [];
if ($acao === 'listar') {
    $busca = $_GET['busca'] ?? '';
    $tipo_pessoa = $_GET['tipo_pessoa'] ?? '';
    
    $sql = "SELECT * FROM clientes WHERE 1=1";
    $params = [];
    
    if ($busca) {
        $sql .= " AND (nome LIKE ? OR cpf_cnpj LIKE ? OR telefone LIKE ? OR celular LIKE ? OR email LIKE ?)";
        $busca_param = "%{$busca}%";
        $params = array_fill(0, 5, $busca_param);
    }
    
    if ($tipo_pessoa) {
        $sql .= " AND tipo_pessoa = ?";
        $params[] = $tipo_pessoa;
    }
    
    $sql .= " ORDER BY nome ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $clientes = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Clientes - NM Refrigeração</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <div class="layout-admin">
        <!-- Sidebar (mesma do dashboard) -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <img src="/assets/img/logo.png" alt="Logo" onerror="this.style.display='none'">
                <h2>NM Refrigeração</h2>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-titulo">Gestão Geral</div>
                    <a href="/admin/index.php" class="nav-link"><i>📊</i><span>Dashboard</span></a>
                    <a href="/admin/clientes.php" class="nav-link ativo"><i>👥</i><span>Clientes</span></a>
                    <a href="/admin/produtos.php" class="nav-link"><i>📦</i><span>Produtos</span></a>
                    <a href="/admin/servicos.php" class="nav-link"><i>🔧</i><span>Serviços</span></a>
                    <a href="/admin/pedidos.php" class="nav-link"><i>🛒</i><span>Pedidos</span></a>
                    <a href="/admin/orcamentos.php" class="nav-link"><i>📝</i><span>Orçamentos</span></a>
                    <a href="/admin/agendamentos.php" class="nav-link"><i>📅</i><span>Agendamentos</span></a>
                    <a href="/admin/vendas.php" class="nav-link"><i>💰</i><span>Vendas</span></a>
                    <a href="/admin/cobrancas.php" class="nav-link"><i>💳</i><span>Cobranças</span></a>
                </div>
                <div class="nav-section">
                    <div class="nav-section-titulo">Técnico AC</div>
                    <a href="/admin/ac-orcamentos.php" class="nav-link"><i>📋</i><span>Orçamentos AC</span></a>
                    <a href="/admin/garantias.php" class="nav-link"><i>🛡️</i><span>Garantias</span></a>
                    <a href="/admin/relatorios.php" class="nav-link"><i>📊</i><span>Relatórios</span></a>
                    <a href="/admin/financeiro.php" class="nav-link"><i>💹</i><span>Financeiro</span></a>
                    <a href="/admin/pmp.php" class="nav-link"><i>⚙️</i><span>PMP</span></a>
                </div>
                <div class="nav-section">
                    <div class="nav-section-titulo">Sistema</div>
                    <a href="/admin/configuracoes.php" class="nav-link"><i>⚙️</i><span>Configurações</span></a>
                    <a href="#" class="nav-link btn-logout"><i>🚪</i><span>Sair</span></a>
                </div>
            </nav>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="conteudo-principal">
            <!-- Header -->
            <header class="header-admin">
                <div class="header-left">
                    <button class="btn-toggle-sidebar">☰</button>
                    <h1 class="header-titulo">Clientes</h1>
                </div>
                <div class="header-right">
                    <div class="header-search">
                        <i>🔍</i>
                        <input type="text" placeholder="Buscar...">
                    </div>
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
                    <div class="breadcrumb-item">Clientes</div>
                    <?php if ($acao === 'novo'): ?>
                    <div class="breadcrumb-item">Novo Cliente</div>
                    <?php elseif ($acao === 'editar'): ?>
                    <div class="breadcrumb-item">Editar Cliente</div>
                    <?php endif; ?>
                </div>

                <?php if ($acao === 'listar'): ?>
                <!-- Modo Listagem -->
                
                <!-- Estatísticas -->
                <div class="cards-estatisticas">
                    <div class="card-stat">
                        <div class="card-stat-header">
                            <div>
                                <div class="card-stat-valor"><?= number_format($total_clientes) ?></div>
                                <div class="card-stat-label">Total de Clientes</div>
                            </div>
                            <div class="card-stat-icone azul">👥</div>
                        </div>
                    </div>
                </div>

                <!-- Card de Listagem -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-titulo">Pesquisar Clientes</h3>
                        <div class="d-flex gap-2">
                            <button onclick="importarContatos()" class="btn btn-outline">
                                <span>📱</span> Importar Agenda
                            </button>
                            <a href="?acao=novo" class="btn btn-primario">
                                <span>➕</span> Novo Cliente
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filtros -->
                        <form method="GET" class="mb-3">
                            <div class="d-flex gap-2 flex-wrap">
                                <input type="text" name="busca" class="form-input" placeholder="Buscar por nome, CPF/CNPJ, telefone..." 
                                       value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>" style="flex: 1; min-width: 250px;">
                                
                                <select name="tipo_pessoa" class="form-select" style="width: 180px;">
                                    <option value="">Todos os Tipos</option>
                                    <option value="fisica" <?= ($_GET['tipo_pessoa'] ?? '') === 'fisica' ? 'selected' : '' ?>>Pessoa Física</option>
                                    <option value="juridica" <?= ($_GET['tipo_pessoa'] ?? '') === 'juridica' ? 'selected' : '' ?>>Pessoa Jurídica</option>
                                </select>
                                
                                <button type="submit" class="btn btn-primario">🔍 Buscar</button>
                                <a href="?" class="btn btn-outline">🔄 Limpar</a>
                            </div>
                        </form>

                        <!-- Tabela de Clientes -->
                        <div class="tabela-wrapper">
                            <table class="tabela" id="tabela-clientes">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Tipo</th>
                                        <th>CPF/CNPJ</th>
                                        <th>Telefone</th>
                                        <th>Email</th>
                                        <th>Cidade</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($clientes)): ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center; padding: 2rem; color: var(--cor-texto-claro);">
                                            Nenhum cliente encontrado
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($clientes as $cli): ?>
                                    <tr>
                                        <td><?= $cli['id'] ?></td>
                                        <td><strong><?= htmlspecialchars($cli['nome']) ?></strong></td>
                                        <td>
                                            <span class="badge badge-<?= $cli['tipo_pessoa'] === 'fisica' ? 'primario' : 'info' ?>">
                                                <?= $cli['tipo_pessoa'] === 'fisica' ? 'Física' : 'Jurídica' ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($cli['cpf_cnpj'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($cli['celular'] ?? $cli['telefone'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($cli['email'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($cli['cidade'] ?? '-') ?></td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button onclick="verCliente(<?= $cli['id'] ?>)" class="btn btn-sm btn-outline" title="Ver Detalhes">
                                                    👁️
                                                </button>
                                                <a href="?acao=editar&id=<?= $cli['id'] ?>" class="btn btn-sm btn-primario" title="Editar">
                                                    ✏️
                                                </a>
                                                <button onclick="excluirCliente(<?= $cli['id'] ?>, '<?= htmlspecialchars($cli['nome']) ?>')" 
                                                        class="btn btn-sm btn-erro" title="Excluir">
                                                    🗑️
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <?php elseif ($acao === 'novo' || $acao === 'editar'): ?>
                <!-- Modo Cadastro/Edição -->
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-titulo">
                            <?= $acao === 'novo' ? '➕ Novo Cliente' : '✏️ Editar Cliente' ?>
                        </h3>
                        <a href="?" class="btn btn-outline">← Voltar</a>
                    </div>
                    <div class="card-body">
                        <form id="form-cliente" method="POST" action="/api/clientes.php">
                            <input type="hidden" name="acao" value="<?= $acao === 'novo' ? 'criar' : 'atualizar' ?>">
                            <?php if ($cliente): ?>
                            <input type="hidden" name="id" value="<?= $cliente['id'] ?>">
                            <?php endif; ?>

                            <!-- Dados do Cliente -->
                            <h4 class="mb-2">Dados do Cliente</h4>
                            
                            <div class="form-group">
                                <label class="form-label">Tipo de Pessoa *</label>
                                <div class="d-flex gap-3">
                                    <label style="display: flex; align-items: center; gap: 0.5rem;">
                                        <input type="radio" name="tipo_pessoa" value="fisica" 
                                               <?= (!$cliente || $cliente['tipo_pessoa'] === 'fisica') ? 'checked' : '' ?> 
                                               onchange="toggleTipoPessoa()">
                                        <span>Pessoa Física</span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 0.5rem;">
                                        <input type="radio" name="tipo_pessoa" value="juridica" 
                                               <?= ($cliente && $cliente['tipo_pessoa'] === 'juridica') ? 'checked' : '' ?>
                                               onchange="toggleTipoPessoa()">
                                        <span>Pessoa Jurídica</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" id="label-nome">
                                    <span class="label-fisica">Nome Completo *</span>
                                    <span class="label-juridica" style="display: none;">Razão Social *</span>
                                </label>
                                <input type="text" name="nome" class="form-input" required
                                       value="<?= htmlspecialchars($cliente['nome'] ?? '') ?>"
                                       placeholder="Digite o nome completo">
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                                <div class="form-group">
                                    <label class="form-label">Telefone</label>
                                    <input type="tel" name="telefone" class="form-input" 
                                           value="<?= htmlspecialchars($cliente['telefone'] ?? '') ?>"
                                           placeholder="(00) 0000-0000">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Celular/WhatsApp *</label>
                                    <input type="tel" name="celular" class="form-input" required
                                           value="<?= htmlspecialchars($cliente['celular'] ?? '') ?>"
                                           placeholder="(00) 00000-0000">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-input"
                                           value="<?= htmlspecialchars($cliente['email'] ?? '') ?>"
                                           placeholder="email@exemplo.com">
                                </div>
                            </div>

                            <!-- Documentos -->
                            <h4 class="mb-2 mt-3">Documentos</h4>
                            
                            <div class="form-group">
                                <label class="form-label" id="label-documento">
                                    <span class="label-fisica">CPF</span>
                                    <span class="label-juridica" style="display: none;">CNPJ</span>
                                </label>
                                <input type="text" name="cpf_cnpj" class="form-input" id="input-documento"
                                       value="<?= htmlspecialchars($cliente['cpf_cnpj'] ?? '') ?>"
                                       placeholder="000.000.000-00">
                            </div>

                            <!-- Endereço -->
                            <h4 class="mb-2 mt-3">Endereço</h4>
                            
                            <div style="display: grid; grid-template-columns: 200px 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label class="form-label">CEP</label>
                                    <input type="text" name="cep" class="form-input" id="input-cep"
                                           value="<?= htmlspecialchars($cliente['cep'] ?? '') ?>"
                                           placeholder="00000-000" onblur="buscarCEP()">
                                    <div class="form-help">Digite o CEP para buscar endereço</div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Logradouro</label>
                                    <input type="text" name="endereco" class="form-input" id="input-endereco"
                                           value="<?= htmlspecialchars($cliente['endereco'] ?? '') ?>"
                                           placeholder="Rua, Avenida, etc">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 120px 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label class="form-label">Número</label>
                                    <input type="text" name="numero" class="form-input"
                                           value="<?= htmlspecialchars($cliente['numero'] ?? '') ?>"
                                           placeholder="123">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Complemento</label>
                                    <input type="text" name="complemento" class="form-input"
                                           value="<?= htmlspecialchars($cliente['complemento'] ?? '') ?>"
                                           placeholder="Apto, Sala, etc">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Bairro</label>
                                    <input type="text" name="bairro" class="form-input" id="input-bairro"
                                           value="<?= htmlspecialchars($cliente['bairro'] ?? '') ?>"
                                           placeholder="Nome do bairro">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 100px; gap: 1rem;">
                                <div class="form-group">
                                    <label class="form-label">Cidade</label>
                                    <input type="text" name="cidade" class="form-input" id="input-cidade"
                                           value="<?= htmlspecialchars($cliente['cidade'] ?? '') ?>"
                                           placeholder="Nome da cidade">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Estado</label>
                                    <input type="text" name="estado" class="form-input" id="input-estado"
                                           value="<?= htmlspecialchars($cliente['estado'] ?? '') ?>"
                                           placeholder="SP" maxlength="2" style="text-transform: uppercase;">
                                </div>
                            </div>

                            <!-- Observações -->
                            <div class="form-group mt-3">
                                <label class="form-label">Observações</label>
                                <textarea name="observacoes" class="form-textarea" rows="4"
                                          placeholder="Informações adicionais sobre o cliente"><?= htmlspecialchars($cliente['observacoes'] ?? '') ?></textarea>
                            </div>

                            <!-- Botões -->
                            <div class="d-flex gap-2 justify-between mt-4">
                                <a href="?" class="btn btn-outline">Cancelar</a>
                                <button type="submit" class="btn btn-primario">
                                    <?= $acao === 'novo' ? '💾 Salvar Cliente' : '✓ Atualizar Cliente' ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="/assets/js/admin.js"></script>
    <script>
        // Toggle entre Pessoa Física e Jurídica
        function toggleTipoPessoa() {
            const tipo = document.querySelector('input[name="tipo_pessoa"]:checked').value;
            const labelsFisica = document.querySelectorAll('.label-fisica');
            const labelsJuridica = document.querySelectorAll('.label-juridica');
            const inputDoc = document.getElementById('input-documento');
            
            if (tipo === 'fisica') {
                labelsFisica.forEach(el => el.style.display = '');
                labelsJuridica.forEach(el => el.style.display = 'none');
                inputDoc.placeholder = '000.000.000-00';
            } else {
                labelsFisica.forEach(el => el.style.display = 'none');
                labelsJuridica.forEach(el => el.style.display = '');
                inputDoc.placeholder = '00.000.000/0000-00';
            }
        }

        // Buscar CEP via API
        async function buscarCEP() {
            const cep = document.getElementById('input-cep').value.replace(/\D/g, '');
            
            if (cep.length !== 8) return;
            
            try {
                const endereco = await Utils.buscarCEP(cep);
                document.getElementById('input-endereco').value = endereco.logradouro;
                document.getElementById('input-bairro').value = endereco.bairro;
                document.getElementById('input-cidade').value = endereco.cidade;
                document.getElementById('input-estado').value = endereco.estado;
                
                Notificacao.sucesso('CEP encontrado!');
            } catch (error) {
                Notificacao.erro('CEP não encontrado');
            }
        }

        // Salvar cliente (AJAX)
        document.getElementById('form-cliente')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            
            // Validações
            if (!Utils.validarTelefone(data.celular)) {
                Notificacao.erro('Celular inválido');
                return;
            }
            
            if (data.email && !Utils.validarEmail(data.email)) {
                Notificacao.erro('Email inválido');
                return;
            }
            
            if (data.cpf_cnpj) {
                const valido = data.tipo_pessoa === 'fisica' 
                    ? Utils.validarCPF(data.cpf_cnpj)
                    : Utils.validarCNPJ(data.cpf_cnpj);
                    
                if (!valido) {
                    Notificacao.erro(data.tipo_pessoa === 'fisica' ? 'CPF inválido' : 'CNPJ inválido');
                    return;
                }
            }
            
            try {
                const method = data.acao === 'criar' ? 'POST' : 'PUT';
                const endpoint = data.acao === 'criar' ? '/clientes' : `/clientes/${data.id}`;
                
                const response = await app.apiRequest(method, endpoint, data);
                
                Notificacao.sucesso(response.mensagem || 'Cliente salvo com sucesso!');
                
                setTimeout(() => {
                    window.location.href = '/admin/clientes.php';
                }, 1500);
            } catch (error) {
                Notificacao.erro(error.message || 'Erro ao salvar cliente');
            }
        });

        // Ver detalhes do cliente
        function verCliente(id) {
            window.location.href = `?acao=editar&id=${id}`;
        }

        // Excluir cliente
        function excluirCliente(id, nome) {
            Modal.confirmar(
                `Deseja realmente excluir o cliente <strong>${nome}</strong>?<br><br>Esta ação não pode ser desfeita.`,
                async () => {
                    try {
                        await app.apiRequest('DELETE', `/clientes/${id}`);
                        Notificacao.sucesso('Cliente excluído com sucesso!');
                        setTimeout(() => location.reload(), 1500);
                    } catch (error) {
                        Notificacao.erro(error.message || 'Erro ao excluir cliente');
                    }
                }
            );
        }

        // Importar contatos da agenda
        function importarContatos() {
            Modal.criar({
                titulo: '📱 Importar Agenda do Celular',
                conteudo: `
                    <p>Esta funcionalidade permite importar contatos da sua agenda telefônica.</p>
                    <div class="alerta alerta-info">
                        <strong>Instruções:</strong>
                        <ol style="margin: 0.5rem 0 0 1.5rem;">
                            <li>Exporte sua agenda como arquivo VCF ou CSV</li>
                            <li>Faça upload do arquivo abaixo</li>
                            <li>Revise os contatos antes de importar</li>
                        </ol>
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">Arquivo de Contatos</label>
                        <input type="file" accept=".vcf,.csv" class="form-input">
                    </div>
                `,
                botoes: [
                    { texto: 'Cancelar', classe: 'btn-outline', acao: 'cancelar' },
                    {
                        texto: 'Importar',
                        classe: 'btn-primario',
                        acao: 'importar',
                        callback: () => {
                            Notificacao.info('Funcionalidade em desenvolvimento');
                        }
                    }
                ]
            });
        }

        // Inicialização
        document.addEventListener('DOMContentLoaded', () => {
            // Aplica máscaras nos campos
            const inputCelular = document.querySelector('input[name="celular"]');
            if (inputCelular) {
                inputCelular.addEventListener('input', (e) => {
                    let valor = e.target.value.replace(/\D/g, '');
                    if (valor.length <= 11) {
                        valor = valor.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
                        e.target.value = valor;
                    }
                });
            }

            const inputCEP = document.getElementById('input-cep');
            if (inputCEP) {
                inputCEP.addEventListener('input', (e) => {
                    let valor = e.target.value.replace(/\D/g, '');
                    valor = valor.replace(/^(\d{5})(\d{3}).*/, '$1-$2');
                    e.target.value = valor;
                });
            }

            const inputDoc = document.getElementById('input-documento');
            if (inputDoc) {
                inputDoc.addEventListener('input', (e) => {
                    const tipo = document.querySelector('input[name="tipo_pessoa"]:checked').value;
                    let valor = e.target.value.replace(/\D/g, '');
                    
                    if (tipo === 'fisica') {
                        valor = valor.replace(/^(\d{3})(\d{3})(\d{3})(\d{2}).*/, '$1.$2.$3-$4');
                    } else {
                        valor = valor.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2}).*/, '$1.$2.$3/$4-$5');
                    }
                    
                    e.target.value = valor;
                });
            }
        });
    </script>
</body>
</html>
