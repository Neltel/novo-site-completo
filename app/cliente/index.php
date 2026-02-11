<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal do Cliente - NM Refrigeração">
    <title>Portal do Cliente - NM Refrigeração</title>
    <style>
        :root {
            --cor-primaria: #6366F1;
            --cor-primaria-escuro: #4F46E5;
            --cor-fundo: #0F172A;
            --cor-fundo-cartao: #1E293B;
            --cor-texto: #E2E8F0;
            --cor-texto-secundario: #CBD5E1;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, var(--cor-fundo) 0%, #0F1419 100%);
            color: var(--cor-texto);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            width: 100%;
            background: var(--cor-fundo-cartao);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        h1 {
            color: var(--cor-primaria);
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        p {
            color: var(--cor-texto-secundario);
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-primary {
            background: var(--cor-primaria);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--cor-primaria-escuro);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--cor-primaria);
            border: 2px solid var(--cor-primaria);
        }
        
        .btn-secondary:hover {
            background: var(--cor-primaria);
            color: white;
            transform: translateY(-2px);
        }
        
        .info-box {
            background: rgba(99, 102, 241, 0.1);
            border-left: 4px solid var(--cor-primaria);
            padding: 1rem;
            margin-top: 2rem;
            border-radius: 4px;
        }
        
        .info-box h2 {
            color: var(--cor-primaria);
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        
        .info-box ul {
            list-style: none;
            padding-left: 0;
        }
        
        .info-box li {
            color: var(--cor-texto-secundario);
            padding: 0.3rem 0;
        }
        
        .info-box li:before {
            content: "• ";
            color: var(--cor-primaria);
            font-weight: bold;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Portal do Cliente</h1>
        <p>Bem-vindo ao Portal do Cliente da NM Refrigeração. Aqui você pode gerenciar suas solicitações, acompanhar orçamentos e muito mais.</p>
        
        <div class="buttons">
            <a href="/login.html" class="btn btn-primary">Fazer Login</a>
            <a href="https://github.com/Neltel/novo-site-completo#readme" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">Documentação</a>
        </div>
        
        <div class="info-box">
            <h2>Funcionalidades Disponíveis:</h2>
            <ul>
                <li>Acompanhamento de orçamentos em tempo real</li>
                <li>Histórico de serviços realizados</li>
                <li>Solicitação de manutenções preventivas</li>
                <li>Gestão de garantias</li>
                <li>Acesso a relatórios e documentos</li>
            </ul>
        </div>
    </div>
</body>
</html>
