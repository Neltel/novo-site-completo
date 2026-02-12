# 🚀 Guia de Instalação e Deploy
## Sistema NM Refrigeração - Gestão Integrada Completa

### 📋 Visão Geral

Este é um sistema completo de gestão empresarial que integra 3 aplicações em uma:
- **App-1**: Gestão geral de negócios (9 módulos)
- **App-2**: Gestão técnica de ar condicionado (11 módulos)
- **Website**: Portal do cliente com agendamento online

---

## 🎯 Pré-Requisitos

### Servidor
- **PHP**: 7.4 ou superior
- **MySQL**: 5.7 ou superior (ou MariaDB 10.2+)
- **Apache**: 2.4+ com mod_rewrite ativado
- **Espaço em Disco**: Mínimo 100MB

### Extensões PHP Necessárias
```bash
php-mysql
php-pdo
php-json
php-mbstring
php-curl
```

Verificar se estão instaladas:
```bash
php -m | grep -E 'mysql|pdo|json|mbstring|curl'
```

---

## 📦 Instalação Passo a Passo

### 1. Upload dos Arquivos

#### Opção A: Via FTP/SFTP
1. Conecte-se ao seu servidor FTP
2. Navegue até: `domains/novo.nmrefrigeracao.business/public_html/`
3. Faça upload de **TODOS** os arquivos do repositório
4. Certifique-se de manter a estrutura de pastas intacta

#### Opção B: Via SSH (se disponível)
```bash
cd /home/seu-usuario/domains/novo.nmrefrigeracao.business/public_html/
git clone https://github.com/Neltel/novo-site-completo.git .
```

### 2. Configurar Permissões

Defina permissões corretas para pastas de upload e logs:

```bash
chmod 755 public_html
chmod 777 public/uploads
chmod 777 public/logs
chmod 644 .env
```

### 3. Banco de Dados

#### O banco já está criado!
Suas credenciais (já configuradas no sistema):
```
Host: localhost
Database: nmrefrig_imperio
Username: nmrefrig_imperio
Password: JEJ5qnvpLRbACP7tUhu6
```

#### Importar Estrutura
Acesse via navegador:
```
https://novo.nmrefrigeracao.business/install.php
```

Ou via linha de comando:
```bash
mysql -u nmrefrig_imperio -p nmrefrig_imperio < database/schema.sql
# Senha: JEJ5qnvpLRbACP7tUhu6
```

O instalador irá:
- ✅ Criar 30 tabelas
- ✅ Criar usuário admin padrão
- ✅ Inserir configurações iniciais
- ✅ Verificar conexões

### 4. Executar Instalador

1. Acesse: `https://novo.nmrefrigeracao.business/install.php`
2. Clique em "Instalar Sistema"
3. Aguarde conclusão (15-30 segundos)
4. Anote as credenciais do administrador:
   - **Email**: admin@nmrefrigeracao.business
   - **Senha**: admin123456

⚠️ **IMPORTANTE**: Após instalação bem-sucedida:
```bash
rm install.php
```

---

## 🔐 Primeiro Acesso

### 1. Login Admin
1. Acesse: `https://novo.nmrefrigeracao.business/login.html`
2. Use as credenciais criadas na instalação
3. **ALTERE A SENHA IMEDIATAMENTE** em Configurações

### 2. Configurar Empresa
1. Vá para: **Admin → Configurações**
2. Preencha dados da empresa:
   - Nome da empresa
   - CNPJ
   - Endereço completo
   - Telefone/WhatsApp
   - Email
   - Logo (upload)

### 3. Testar Sistema
Execute o teste completo:
```
https://novo.nmrefrigeracao.business/teste-completo.php
```

Este script verifica:
- ✓ Conexão com banco de dados
- ✓ Todas as 30 tabelas
- ✓ Permissões de arquivos
- ✓ Extensões PHP
- ✓ APIs disponíveis

---

## 📂 Estrutura de Arquivos

```
novo.nmrefrigeracao.business/
├── admin/                      # Painel administrativo
│   ├── index.php              # Dashboard principal
│   ├── clientes.php           # Gestão de clientes
│   ├── produtos.php           # Gestão de produtos
│   ├── servicos.php           # Gestão de serviços
│   ├── agendamentos.php       # Agenda/calendário
│   ├── vendas.php             # Controle de vendas
│   ├── financeiro.php         # Módulo financeiro
│   └── ...                    # Outros módulos
│
├── cliente/                   # Portal do cliente (público)
│   └── index.php              # Homepage + agendamento
│
├── api/                       # Endpoints da API REST
│   ├── auth.php               # Autenticação
│   ├── clientes.php           # CRUD clientes
│   ├── produtos.php           # CRUD produtos
│   ├── servicos.php           # CRUD serviços
│   ├── agendamentos.php       # CRUD agendamentos
│   └── ...                    # Outros endpoints
│
├── assets/                    # Arquivos estáticos
│   ├── css/
│   │   ├── admin.css          # Estilos admin (900 linhas)
│   │   └── cliente.css        # Estilos cliente (600 linhas)
│   ├── js/
│   │   └── admin.js           # JavaScript admin (1000+ linhas)
│   └── img/                   # Imagens
│
├── classes/                   # Classes PHP
│   ├── Database.php           # Conexão BD
│   ├── Auth.php               # Autenticação
│   ├── PDF.php                # Geração PDF
│   └── WhatsApp.php           # Integração WhatsApp
│
├── config/                    # Configurações
│   ├── config.php             # Config geral
│   ├── database.php           # Config BD
│   └── constants.php          # Constantes
│
├── database/                  # Banco de dados
│   └── schema.sql             # Estrutura completa (30 tabelas)
│
├── public/                    # Uploads e logs
│   ├── uploads/               # Arquivos enviados
│   └── logs/                  # Logs do sistema
│
├── .env                       # Variáveis de ambiente
├── .htaccess                  # Reescrita de URL
├── index.php                  # Roteador principal
├── login.html                 # Página de login
└── README.md                  # Este arquivo
```

---

## 🎨 Personalização

### 1. Logo da Empresa
1. Prepare logo em PNG (recomendado 200x200px)
2. Admin → Configurações → Logo da Empresa
3. Faça upload
4. Logo aparecerá em:
   - Painel admin
   - Site cliente
   - PDFs
   - Emails

### 2. Cores do Sistema
Edite `assets/css/admin.css` e `assets/css/cliente.css`:

```css
:root {
    --cor-primaria: #2563eb;      /* Azul - mude para sua cor */
    --cor-secundaria: #10b981;    /* Verde */
    --cor-primaria-escura: #1e40af;
}
```

### 3. Informações de Contato
Admin → Configurações → Dados da Empresa

---

## 🔧 Configurações Avançadas

### Apache mod_rewrite

Certifique-se de que está ativado:

```apache
# Em httpd.conf ou apache2.conf
LoadModule rewrite_module modules/mod_rewrite.so

# No VirtualHost
<Directory /path/to/public_html>
    AllowOverride All
    Require all granted
</Directory>
```

Reiniciar Apache:
```bash
sudo systemctl restart apache2
# ou
sudo service httpd restart
```

### PHP.ini Recomendações

```ini
upload_max_filesize = 10M
post_max_size = 12M
max_execution_time = 300
memory_limit = 256M
date.timezone = America/Sao_Paulo
```

### HTTPS (SSL)

**IMPORTANTE**: Configure SSL/HTTPS antes de usar em produção!

Com cPanel:
1. Vá em: SSL/TLS Status
2. Selecione seu domínio
3. Clique em "Run AutoSSL"

Ou use Let's Encrypt:
```bash
certbot --apache -d novo.nmrefrigeracao.business
```

---

## 📱 Integrações

### WhatsApp

1. Obtenha número WhatsApp Business
2. Configure em: Admin → Configurações → Integrações
3. Formato: +5511999999999

**Funcionalidades**:
- ✅ Agendamentos via WhatsApp
- ✅ Notificações automáticas
- ✅ Confirmações de serviço
- ✅ Lembretes 1 dia/hora antes

### API de IA (Assistente Virtual)

Para usar o assistente de ar condicionado:

1. Obtenha chave API OpenAI em: https://platform.openai.com/
2. Configure em `.env`:
```env
IA_API_KEY=sk-...
IA_MODEL=gpt-3.5-turbo
```

### Instagram Feed

Configure token de acesso:
1. Acesse: https://developers.facebook.com/
2. Crie app do Instagram
3. Obtenha Access Token
4. Salve em: Admin → Configurações → Integrações

---

## 🔒 Segurança

### Checklist de Segurança

- [ ] HTTPS ativado (SSL)
- [ ] Senha do admin alterada
- [ ] Arquivo `install.php` deletado
- [ ] Permissões corretas (755 pastas, 644 arquivos)
- [ ] `.env` não acessível via web (já protegido)
- [ ] Backup automático configurado
- [ ] Atualizar PHP regularmente
- [ ] Firewall ativo

### Backup

Configure backup automático via cPanel ou:

```bash
# Script de backup diário
#!/bin/bash
mysqldump -u nmrefrig_imperio -p'JEJ5qnvpLRbACP7tUhu6' nmrefrig_imperio > backup_$(date +%Y%m%d).sql
tar -czf backup_files_$(date +%Y%m%d).tar.gz /path/to/public_html
```

Adicione ao cron:
```bash
0 2 * * * /path/to/backup.sh
```

---

## 📚 Uso do Sistema

### Fluxo Básico

1. **Cliente acessa site** (/)
   - Vê catálogo de serviços
   - Calcula carga térmica
   - Agenda serviço online

2. **Sistema notifica admin**
   - WhatsApp automático
   - Email de novo agendamento
   - Aparece no dashboard

3. **Admin confirma** (admin/agendamentos.php)
   - Revisa dados
   - Confirma data/hora
   - Atribui técnico

4. **Execução do serviço**
   - Técnico recebe notificação
   - Acessa via app/admin
   - Registra execução

5. **Pós-serviço**
   - Gera garantia
   - Envia recibo
   - Registra pagamento

### Módulos Principais

#### 1. Clientes (admin/clientes.php)
- Cadastro completo
- CPF/CNPJ validado
- CEP automático
- Histórico de serviços
- Importar agenda celular

#### 2. Produtos (admin/produtos.php)
- Catálogo completo
- Controle de estoque
- Fotos
- Categorias
- Margem de lucro

#### 3. Serviços (admin/servicos.php)
- Cadastro de serviços
- Tempo estimado
- Materiais inclusos
- Preço base
- Exibir para clientes

#### 4. Agendamentos (admin/agendamentos.php)
- Calendário visual
- Datas disponíveis
- Horários
- Notificações
- Status do serviço

#### 5. Orçamentos (admin/orcamentos.php)
- Criar orçamento
- Produtos + Serviços
- Desconto %
- Gerar PDF
- Enviar WhatsApp

#### 6. Vendas (admin/vendas.php)
- Registro de vendas
- Formas de pagamento
- Valor bruto/custo/lucro
- Gráficos mensais

#### 7. Financeiro (admin/financeiro.php)
- Entradas/Saídas
- Extratos mensais
- Lucro real
- Exportar para contador

#### 8. Garantias (admin/garantias.php)
- Emitir garantias
- Termos legais (conforme Lei brasileira)
- Fotos do serviço
- Enviar PDF/WhatsApp

#### 9. PMP (admin/pmp.php)
- Planos de manutenção
- Contratos periódicos
- Check-lists automáticos
- Notificações programadas

---

## 🐛 Troubleshooting

### Erro: "Página em Branco"
```bash
# Ativar display de erros temporariamente
# Edite index.php, adicione no topo:
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Erro: "Conexão com Banco Falhou"
- Verifique credenciais em `.env`
- Teste conexão MySQL:
```bash
mysql -u nmrefrig_imperio -p -h localhost
```

### Erro: "404 em todas URLs"
- Mod_rewrite não está ativo
- `.htaccess` não está sendo lido
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Erro: "Upload de Imagem Falha"
- Verifique permissões:
```bash
chmod 777 public/uploads
```

### Erro: "Session não funciona"
- Verificar permissão da pasta de sessões:
```bash
chmod 777 /var/lib/php/sessions
# ou configurar em php.ini
session.save_path = "/caminho/writable"
```

---

## 📞 Suporte

### Logs do Sistema

Ver erros do PHP:
```bash
tail -f public/logs/app-*.log
```

Ver logs do Apache:
```bash
tail -f /var/log/apache2/error.log
```

### Teste de Endpoints

Testar API:
```bash
curl -X POST https://novo.nmrefrigeracao.business/api/auth.php \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@nmrefrigeracao.business","senha":"admin123456"}'
```

### Comandos Úteis

```bash
# Ver info PHP
php -i | grep -E 'version|extension'

# Testar sintaxe
php -l arquivo.php

# Ver processos MySQL
mysqladmin -u root -p processlist

# Espaço em disco
df -h
```

---

## 🎓 Recursos de Aprendizado

### Documentação Técnica
- `API_ENDPOINTS_DOCS.md` - Documentação completa da API
- `DOCUMENTACAO.md` - Documentação geral do sistema
- `database/SCHEMA_DOCUMENTATION.md` - Documentação do banco

### Exemplos de Código
- `exemplo-auth-api.php` - Exemplo de autenticação
- `api/exemplo-uso-api.php` - Exemplos de uso da API

### Comentários no Código
Todos os arquivos contêm comentários detalhados em português explicando:
- O que cada função faz
- Parâmetros esperados
- Valores de retorno
- Exemplos de uso

---

## ✅ Checklist Pós-Instalação

- [ ] Instalação concluída sem erros
- [ ] Arquivo install.php deletado
- [ ] Login admin funcionando
- [ ] Senha admin alterada
- [ ] SSL/HTTPS configurado
- [ ] Dados da empresa preenchidos
- [ ] Logo uploaded
- [ ] Primeiro cliente cadastrado
- [ ] Primeiro serviço cadastrado
- [ ] Agendamento teste realizado
- [ ] Backup configurado
- [ ] WhatsApp configurado (opcional)
- [ ] Email SMTP configurado (opcional)
- [ ] Instagram integrado (opcional)
- [ ] IA configurada (opcional)

---

## 🚀 Performance

### Otimizações Recomendadas

1. **Cache**:
```apache
# Em .htaccess (já incluído)
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

2. **Gzip**:
```apache
# Já ativado no .htaccess
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript
</IfModule>
```

3. **OPcache** (PHP):
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

---

## 📊 Monitoramento

### Métricas Importantes

- Tempo de resposta (< 2s)
- Uso de memória
- Queries lentas no MySQL
- Taxa de erro 500
- Uptime do servidor

### Ferramentas

- Google Analytics (já preparado)
- Google Search Console
- cPanel Metrics
- New Relic (opcional)
- Pingdom (opcional)

---

## 🔄 Atualizações

### Como Atualizar

```bash
cd public_html
git pull origin main
# Verificar se há migrações de banco
php migrate.php
```

### Changelog

Acompanhe atualizações em: `CHANGELOG.md`

---

## 📝 Notas Finais

Este sistema foi desenvolvido com:
- ✅ Segurança em mente (preparadas statements, validações)
- ✅ Performance otimizada (cache, compressão)
- ✅ Mobile-first (100% responsivo)
- ✅ SEO-friendly (meta tags, URLs limpas)
- ✅ Acessibilidade (ARIA labels, contraste)
- ✅ Manutenibilidade (código limpo, comentado)

**Desenvolvido para**: NM Refrigeração
**Data**: Fevereiro 2024
**Versão**: 1.0.0

---

## 📧 Contato

Para suporte ou dúvidas sobre o sistema:
- Email: suporte@nmrefrigeracao.business
- WhatsApp: (11) 99999-9999

---

**Última atualização**: <?= date('d/m/Y') ?>
