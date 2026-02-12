/**
 * JavaScript Principal do Painel Administrativo
 * Sistema NM Refrigeração - Gestão Integrada
 * 
 * Funções: Navegação, AJAX, Validação, UI
 * Uso: Todas as páginas do painel admin
 */

// ========== CONFIGURAÇÃO GLOBAL ==========
const CONFIG = {
    API_BASE_URL: '/api',
    TIMEOUT: 30000,
    ITEMS_PER_PAGE: 20
};

// ========== CLASSE PRINCIPAL ==========
class AdminApp {
    constructor() {
        this.sidebar = null;
        this.conteudo = null;
        this.usuario = null;
        this.init();
    }

    /**
     * Inicializa a aplicação
     * Chamado automaticamente no construtor
     */
    init() {
        this.sidebar = document.querySelector('.sidebar');
        this.conteudo = document.querySelector('.conteudo-principal');
        this.carregarUsuario();
        this.configurarEventos();
        this.marcarMenuAtivo();
        console.log('Admin App inicializado');
    }

    /**
     * Carrega informações do usuário logado
     * Usa sessionStorage para armazenar dados
     */
    carregarUsuario() {
        const usuarioData = sessionStorage.getItem('usuario');
        if (usuarioData) {
            this.usuario = JSON.parse(usuarioData);
        }
    }

    /**
     * Configura todos os event listeners
     */
    configurarEventos() {
        // Toggle sidebar mobile
        const btnToggle = document.querySelector('.btn-toggle-sidebar');
        if (btnToggle) {
            btnToggle.addEventListener('click', () => this.toggleSidebar());
        }

        // Fechar sidebar ao clicar fora (mobile)
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                if (!this.sidebar.contains(e.target) && !e.target.closest('.btn-toggle-sidebar')) {
                    this.sidebar?.classList.remove('aberta');
                }
            }
        });

        // Logout
        const btnLogout = document.querySelector('.btn-logout');
        if (btnLogout) {
            btnLogout.addEventListener('click', (e) => {
                e.preventDefault();
                this.logout();
            });
        }
    }

    /**
     * Alterna visibilidade da sidebar
     */
    toggleSidebar() {
        if (window.innerWidth <= 768) {
            this.sidebar?.classList.toggle('aberta');
        } else {
            this.sidebar?.classList.toggle('fechada');
            this.conteudo?.classList.toggle('expandido');
        }
    }

    /**
     * Marca item do menu como ativo baseado na URL
     */
    marcarMenuAtivo() {
        const path = window.location.pathname;
        const links = document.querySelectorAll('.nav-link');
        
        links.forEach(link => {
            link.classList.remove('ativo');
            if (link.getAttribute('href') === path) {
                link.classList.add('ativo');
            }
        });
    }

    /**
     * Realiza logout do usuário
     */
    async logout() {
        if (confirm('Deseja realmente sair do sistema?')) {
            try {
                await this.apiRequest('POST', '/auth/logout');
                sessionStorage.clear();
                localStorage.clear();
                window.location.href = '/login.html';
            } catch (error) {
                console.error('Erro ao fazer logout:', error);
                window.location.href = '/login.html';
            }
        }
    }

    /**
     * Faz requisição para API
     * @param {string} method - GET, POST, PUT, DELETE
     * @param {string} endpoint - Caminho do endpoint
     * @param {object} data - Dados a enviar
     * @returns {Promise} Resposta da API
     */
    async apiRequest(method, endpoint, data = null) {
        const url = `${CONFIG.API_BASE_URL}${endpoint}`;
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            }
        };

        // Adiciona token de autenticação se existir
        const token = sessionStorage.getItem('token');
        if (token) {
            options.headers['Authorization'] = `Bearer ${token}`;
        }

        // Adiciona body para POST/PUT
        if (data && (method === 'POST' || method === 'PUT')) {
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, options);
            const json = await response.json();

            if (!response.ok) {
                throw new Error(json.mensagem || 'Erro na requisição');
            }

            return json;
        } catch (error) {
            console.error(`Erro na requisição ${method} ${endpoint}:`, error);
            throw error;
        }
    }
}

// ========== CLASSE DE UTILIDADES ==========
class Utils {
    /**
     * Formata valor monetário
     * @param {number} valor - Valor numérico
     * @returns {string} Valor formatado (R$ 1.234,56)
     */
    static formatarMoeda(valor) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(valor);
    }

    /**
     * Formata data
     * @param {string|Date} data - Data para formatar
     * @returns {string} Data formatada (dd/mm/yyyy)
     */
    static formatarData(data) {
        const d = new Date(data);
        return d.toLocaleDateString('pt-BR');
    }

    /**
     * Formata data e hora
     * @param {string|Date} data - Data para formatar
     * @returns {string} Data e hora formatadas (dd/mm/yyyy HH:mm)
     */
    static formatarDataHora(data) {
        const d = new Date(data);
        return d.toLocaleString('pt-BR');
    }

    /**
     * Valida CPF
     * @param {string} cpf - CPF para validar
     * @returns {boolean} true se válido
     */
    static validarCPF(cpf) {
        cpf = cpf.replace(/[^\d]/g, '');
        
        if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
            return false;
        }

        let soma = 0;
        let resto;

        for (let i = 1; i <= 9; i++) {
            soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
        }

        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf.substring(9, 10))) return false;

        soma = 0;
        for (let i = 1; i <= 10; i++) {
            soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
        }

        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf.substring(10, 11))) return false;

        return true;
    }

    /**
     * Valida CNPJ
     * @param {string} cnpj - CNPJ para validar
     * @returns {boolean} true se válido
     */
    static validarCNPJ(cnpj) {
        cnpj = cnpj.replace(/[^\d]/g, '');

        if (cnpj.length !== 14 || /^(\d)\1{13}$/.test(cnpj)) {
            return false;
        }

        let tamanho = cnpj.length - 2;
        let numeros = cnpj.substring(0, tamanho);
        let digitos = cnpj.substring(tamanho);
        let soma = 0;
        let pos = tamanho - 7;

        for (let i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }

        let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0)) return false;

        tamanho = tamanho + 1;
        numeros = cnpj.substring(0, tamanho);
        soma = 0;
        pos = tamanho - 7;

        for (let i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }

        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(1)) return false;

        return true;
    }

    /**
     * Valida email
     * @param {string} email - Email para validar
     * @returns {boolean} true se válido
     */
    static validarEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    /**
     * Valida telefone brasileiro
     * @param {string} telefone - Telefone para validar
     * @returns {boolean} true se válido
     */
    static validarTelefone(telefone) {
        const numeros = telefone.replace(/[^\d]/g, '');
        return numeros.length === 10 || numeros.length === 11;
    }

    /**
     * Formata CPF
     * @param {string} cpf - CPF sem formatação
     * @returns {string} CPF formatado (000.000.000-00)
     */
    static formatarCPF(cpf) {
        cpf = cpf.replace(/[^\d]/g, '');
        return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    }

    /**
     * Formata CNPJ
     * @param {string} cnpj - CNPJ sem formatação
     * @returns {string} CNPJ formatado (00.000.000/0000-00)
     */
    static formatarCNPJ(cnpj) {
        cnpj = cnpj.replace(/[^\d]/g, '');
        return cnpj.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    }

    /**
     * Formata telefone
     * @param {string} telefone - Telefone sem formatação
     * @returns {string} Telefone formatado
     */
    static formatarTelefone(telefone) {
        telefone = telefone.replace(/[^\d]/g, '');
        if (telefone.length === 11) {
            return telefone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        } else if (telefone.length === 10) {
            return telefone.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
        }
        return telefone;
    }

    /**
     * Formata CEP
     * @param {string} cep - CEP sem formatação
     * @returns {string} CEP formatado (00000-000)
     */
    static formatarCEP(cep) {
        cep = cep.replace(/[^\d]/g, '');
        return cep.replace(/(\d{5})(\d{3})/, '$1-$2');
    }

    /**
     * Debounce - Atrasa execução de função
     * @param {function} func - Função a executar
     * @param {number} wait - Tempo de espera em ms
     * @returns {function} Função com debounce
     */
    static debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Copia texto para clipboard
     * @param {string} texto - Texto para copiar
     */
    static copiarParaClipboard(texto) {
        navigator.clipboard.writeText(texto).then(() => {
            Notificacao.sucesso('Copiado para área de transferência!');
        }).catch(err => {
            console.error('Erro ao copiar:', err);
        });
    }

    /**
     * Download de arquivo
     * @param {string} url - URL do arquivo
     * @param {string} nome - Nome do arquivo
     */
    static downloadArquivo(url, nome) {
        const a = document.createElement('a');
        a.href = url;
        a.download = nome;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    /**
     * Busca CEP via API
     * @param {string} cep - CEP para buscar
     * @returns {Promise} Dados do endereço
     */
    static async buscarCEP(cep) {
        cep = cep.replace(/[^\d]/g, '');
        
        if (cep.length !== 8) {
            throw new Error('CEP inválido');
        }

        try {
            const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            const data = await response.json();

            if (data.erro) {
                throw new Error('CEP não encontrado');
            }

            return {
                logradouro: data.logradouro,
                bairro: data.bairro,
                cidade: data.localidade,
                estado: data.uf
            };
        } catch (error) {
            console.error('Erro ao buscar CEP:', error);
            throw error;
        }
    }
}

// ========== CLASSE DE NOTIFICAÇÕES ==========
class Notificacao {
    /**
     * Exibe notificação de sucesso
     * @param {string} mensagem - Mensagem a exibir
     * @param {number} duracao - Duração em ms (padrão: 3000)
     */
    static sucesso(mensagem, duracao = 3000) {
        this.mostrar(mensagem, 'sucesso', duracao);
    }

    /**
     * Exibe notificação de erro
     * @param {string} mensagem - Mensagem a exibir
     * @param {number} duracao - Duração em ms (padrão: 5000)
     */
    static erro(mensagem, duracao = 5000) {
        this.mostrar(mensagem, 'erro', duracao);
    }

    /**
     * Exibe notificação de aviso
     * @param {string} mensagem - Mensagem a exibir
     * @param {number} duracao - Duração em ms (padrão: 4000)
     */
    static aviso(mensagem, duracao = 4000) {
        this.mostrar(mensagem, 'aviso', duracao);
    }

    /**
     * Exibe notificação de informação
     * @param {string} mensagem - Mensagem a exibir
     * @param {number} duracao - Duração em ms (padrão: 3000)
     */
    static info(mensagem, duracao = 3000) {
        this.mostrar(mensagem, 'info', duracao);
    }

    /**
     * Exibe notificação
     * @param {string} mensagem - Mensagem
     * @param {string} tipo - Tipo (sucesso, erro, aviso, info)
     * @param {number} duracao - Duração em ms
     */
    static mostrar(mensagem, tipo, duracao) {
        // Remove notificações existentes
        const existentes = document.querySelectorAll('.toast-notificacao');
        existentes.forEach(n => n.remove());

        // Cria elemento da notificação
        const toast = document.createElement('div');
        toast.className = `toast-notificacao toast-${tipo} fade-in`;
        toast.innerHTML = `
            <div class="toast-conteudo">
                <i class="toast-icone ${this.getIcone(tipo)}"></i>
                <span>${mensagem}</span>
            </div>
            <button class="toast-fechar">&times;</button>
        `;

        // Adiciona estilos inline se não existirem
        if (!document.getElementById('toast-styles')) {
            const style = document.createElement('style');
            style.id = 'toast-styles';
            style.textContent = `
                .toast-notificacao {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: white;
                    padding: 1rem 1.5rem;
                    border-radius: 8px;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
                    z-index: 9999;
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                    min-width: 300px;
                    max-width: 500px;
                }
                .toast-conteudo {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    flex: 1;
                }
                .toast-icone {
                    font-size: 1.25rem;
                }
                .toast-fechar {
                    background: none;
                    border: none;
                    font-size: 1.5rem;
                    cursor: pointer;
                    color: #6b7280;
                    padding: 0;
                    line-height: 1;
                }
                .toast-sucesso {
                    border-left: 4px solid #10b981;
                }
                .toast-erro {
                    border-left: 4px solid #ef4444;
                }
                .toast-aviso {
                    border-left: 4px solid #f59e0b;
                }
                .toast-info {
                    border-left: 4px solid #3b82f6;
                }
            `;
            document.head.appendChild(style);
        }

        document.body.appendChild(toast);

        // Evento de fechar
        toast.querySelector('.toast-fechar').addEventListener('click', () => {
            toast.remove();
        });

        // Remove automaticamente após duração
        setTimeout(() => {
            toast.remove();
        }, duracao);
    }

    /**
     * Retorna ícone baseado no tipo
     * @param {string} tipo - Tipo da notificação
     * @returns {string} Nome da classe do ícone
     */
    static getIcone(tipo) {
        const icones = {
            sucesso: '✓',
            erro: '✕',
            aviso: '⚠',
            info: 'ℹ'
        };
        return icones[tipo] || 'ℹ';
    }
}

// ========== CLASSE DE MODAL ==========
class Modal {
    /**
     * Abre modal
     * @param {string} seletor - Seletor CSS do modal
     */
    static abrir(seletor) {
        const modal = document.querySelector(seletor);
        if (modal) {
            modal.classList.add('ativo');
            document.body.style.overflow = 'hidden';
        }
    }

    /**
     * Fecha modal
     * @param {string} seletor - Seletor CSS do modal
     */
    static fechar(seletor) {
        const modal = document.querySelector(seletor);
        if (modal) {
            modal.classList.remove('ativo');
            document.body.style.overflow = '';
        }
    }

    /**
     * Cria modal dinamicamente
     * @param {object} options - Opções do modal
     * @returns {HTMLElement} Elemento do modal
     */
    static criar(options) {
        const {
            titulo = 'Modal',
            conteudo = '',
            botoes = [],
            largura = '600px'
        } = options;

        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay ativo';
        
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.style.maxWidth = largura;
        
        let botoesHtml = '';
        botoes.forEach(btn => {
            botoesHtml += `<button class="btn ${btn.classe || 'btn-primario'}" data-acao="${btn.acao}">${btn.texto}</button>`;
        });

        modal.innerHTML = `
            <div class="modal-header">
                <h3 class="modal-titulo">${titulo}</h3>
                <button class="modal-fechar">&times;</button>
            </div>
            <div class="modal-body">
                ${conteudo}
            </div>
            ${botoesHtml ? `<div class="modal-footer">${botoesHtml}</div>` : ''}
        `;

        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        // Eventos
        overlay.querySelector('.modal-fechar').addEventListener('click', () => {
            overlay.remove();
            document.body.style.overflow = '';
        });

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.remove();
                document.body.style.overflow = '';
            }
        });

        // Eventos dos botões
        botoes.forEach((btn, index) => {
            const elemento = overlay.querySelectorAll('[data-acao]')[index];
            if (elemento && btn.callback) {
                elemento.addEventListener('click', () => {
                    btn.callback();
                    if (btn.fecharAposCl ick !== false) {
                        overlay.remove();
                        document.body.style.overflow = '';
                    }
                });
            }
        });

        document.body.style.overflow = 'hidden';
        return overlay;
    }

    /**
     * Modal de confirmação
     * @param {string} mensagem - Mensagem de confirmação
     * @param {function} callback - Função a executar se confirmado
     */
    static confirmar(mensagem, callback) {
        this.criar({
            titulo: 'Confirmação',
            conteudo: `<p>${mensagem}</p>`,
            botoes: [
                {
                    texto: 'Cancelar',
                    classe: 'btn-outline',
                    acao: 'cancelar'
                },
                {
                    texto: 'Confirmar',
                    classe: 'btn-primario',
                    acao: 'confirmar',
                    callback: callback
                }
            ]
        });
    }
}

// ========== CLASSE DE TABELA DATATABLE ==========
class DataTable {
    constructor(selector, options = {}) {
        this.elemento = document.querySelector(selector);
        this.options = {
            paginacao: options.paginacao !== false,
            busca: options.busca !== false,
            itensPorPagina: options.itensPorPagina || 10,
            colunas: options.colunas || [],
            dados: options.dados || [],
            acoes: options.acoes || null
        };
        this.paginaAtual = 1;
        this.dadosFiltrados = [...this.options.dados];
        this.init();
    }

    init() {
        this.renderizar();
        if (this.options.busca) {
            this.configurarBusca();
        }
    }

    renderizar() {
        let html = '';

        // Busca
        if (this.options.busca) {
            html += `
                <div class="tabela-controles mb-3">
                    <input type="text" class="form-input tabela-busca" placeholder="Buscar..." style="max-width: 300px;">
                </div>
            `;
        }

        // Tabela
        html += '<div class="tabela-wrapper"><table class="tabela"><thead><tr>';
        
        this.options.colunas.forEach(col => {
            html += `<th>${col.titulo}</th>`;
        });
        
        if (this.options.acoes) {
            html += '<th>Ações</th>';
        }
        
        html += '</tr></thead><tbody>';

        // Dados
        const inicio = (this.paginaAtual - 1) * this.options.itensPorPagina;
        const fim = inicio + this.options.itensPorPagina;
        const dadosPagina = this.dadosFiltrados.slice(inicio, fim);

        dadosPagina.forEach(item => {
            html += '<tr>';
            this.options.colunas.forEach(col => {
                let valor = item[col.campo];
                if (col.formatador) {
                    valor = col.formatador(valor, item);
                }
                html += `<td>${valor || '-'}</td>`;
            });

            if (this.options.acoes) {
                html += `<td>${this.options.acoes(item)}</td>`;
            }

            html += '</tr>';
        });

        html += '</tbody></table></div>';

        // Paginação
        if (this.options.paginacao) {
            html += this.renderizarPaginacao();
        }

        this.elemento.innerHTML = html;
    }

    renderizarPaginacao() {
        const totalPaginas = Math.ceil(this.dadosFiltrados.length / this.options.itensPorPagina);
        
        if (totalPaginas <= 1) return '';

        let html = '<div class="paginacao mt-3">';
        
        // Anterior
        html += `<a href="#" class="paginacao-link ${this.paginaAtual === 1 ? 'disabled' : ''}" data-pagina="${this.paginaAtual - 1}">‹</a>`;

        // Páginas
        for (let i = 1; i <= totalPaginas; i++) {
            if (i === 1 || i === totalPaginas || (i >= this.paginaAtual - 2 && i <= this.paginaAtual + 2)) {
                html += `<a href="#" class="paginacao-link ${i === this.paginaAtual ? 'ativo' : ''}" data-pagina="${i}">${i}</a>`;
            } else if (i === this.paginaAtual - 3 || i === this.paginaAtual + 3) {
                html += '<span class="paginacao-link">...</span>';
            }
        }

        // Próxima
        html += `<a href="#" class="paginacao-link ${this.paginaAtual === totalPaginas ? 'disabled' : ''}" data-pagina="${this.paginaAtual + 1}">›</a>`;

        html += '</div>';

        // Adiciona eventos após renderização
        setTimeout(() => {
            document.querySelectorAll('.paginacao-link[data-pagina]').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (!link.classList.contains('disabled')) {
                        this.irParaPagina(parseInt(link.dataset.pagina));
                    }
                });
            });
        }, 0);

        return html;
    }

    configurarBusca() {
        setTimeout(() => {
            const inputBusca = this.elemento.querySelector('.tabela-busca');
            if (inputBusca) {
                inputBusca.addEventListener('input', Utils.debounce((e) => {
                    this.filtrar(e.target.value);
                }, 300));
            }
        }, 0);
    }

    filtrar(termo) {
        termo = termo.toLowerCase();
        
        if (!termo) {
            this.dadosFiltrados = [...this.options.dados];
        } else {
            this.dadosFiltrados = this.options.dados.filter(item => {
                return this.options.colunas.some(col => {
                    const valor = String(item[col.campo] || '').toLowerCase();
                    return valor.includes(termo);
                });
            });
        }

        this.paginaAtual = 1;
        this.renderizar();
    }

    irParaPagina(pagina) {
        this.paginaAtual = pagina;
        this.renderizar();
    }

    atualizar(novosDados) {
        this.options.dados = novosDados;
        this.dadosFiltrados = [...novosDados];
        this.paginaAtual = 1;
        this.renderizar();
    }
}

// ========== INICIALIZAÇÃO ==========
let app;

document.addEventListener('DOMContentLoaded', () => {
    app = new AdminApp();
});

// Exporta para uso global
window.AdminApp = AdminApp;
window.Utils = Utils;
window.Notificacao = Notificacao;
window.Modal = Modal;
window.DataTable = DataTable;
