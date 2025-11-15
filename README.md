# Cofre de Credenciais

Aplicação construída com Laravel 11 + Filament 3 para centralizar credenciais corporativas com fluxo de auditoria completo. O projeto fornece CRUD de credenciais com criptografia, modais seguros para visualização de senhas, painel com indicadores em tempo real e relatórios de acesso.

## Funcionalidades principais

- **Cadastro de credenciais**: formulário dividido em seções (dados principais, segurança e observações) com validações, campo de senha revelável e mutator para criptografia automática.
- **Listagem dinâmica**: tabela com pesquisa, filtros por sistema e ações de visualizar/editar/excluir. A visualização abre um modal compacto que exibe a senha descriptografada e oferece botão de cópia.
- **Auditoria de visualização**: cada abertura do modal gera um registro em `credential_view_logs` com usuário, IP, rota, user agent e metadados adicionais. Os logs são exibidos em um recurso Filament somente leitura com filtros por credencial e usuário.
- **Dashboard executivo**: cards indicam total de credenciais e total de logs. Um gráfico em área cobre 12 meses de cadastros de credenciais, ocupando toda a largura do painel.

## Requisitos

- PHP 8.2+
- Composer 2+
- Node.js 18+ e npm para assets (opcional em dev)
- Banco SQLite (padrão) ou qualquer banco suportado pelo Laravel

## Como executar localmente

1. Instale as dependências:
   ```bash
   composer install
   npm install && npm run build # ou npm run dev para watch
   ```
2. Configure o `.env` (copie de `.env.example`) e gere a chave:
   ```bash
   php artisan key:generate
   ```
3. Execute as migrações e seeds básicos:
   ```bash
   php artisan migrate
   ```
4. Opcional: crie um usuário admin para o Filament.
5. Inicie o servidor:
   ```bash
   php artisan serve
   ```
6. Acesse `http://localhost:8000/admin` e autentique-se.

## Estrutura de diretórios relevantes

- `app/Filament/Resources/CredentialResource.php`: formulário e tabela principal de credenciais.
- `app/Filament/Resources/CredentialViewLogResource.php`: visualização dos logs de auditoria.
- `app/Filament/Pages/Dashboard.php` + `app/Filament/Widgets/*`: widgets do dashboard (indicadores e gráfico mensal).
- `app/Models/Credential.php`: model com mutator/accessor de senha e relacionamentos.
- `app/Models/CredentialViewLog.php`: model de logs.

## Auditoria

A tabela `credential_view_logs` armazena todos os acessos à senha com os campos:
- `credential_id`, `user_id`, `event`
- `ip_address`, `user_agent`, `request_path`
- `meta` (JSON com referer e session id)

Os registros são atomizados via `CredentialResource` sempre que o modal de senha é aberto, garantindo rastreabilidade completa.

## Próximos passos sugeridos

- Aplicar políticas adicionais (aprovação de usuários para visualizar senhas, MFA, etc.).
- Adicionar exportação dos logs para CSV/Excel.
- Implementar notificações automáticas ao detectar acessos suspeitos.

## Licença

Projeto distribuído sob a licença MIT. Veja `LICENSE` para detalhes.
