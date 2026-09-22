# Projeto techAcademy3 

## Nome do projeto
**Plantera - Gestão Escolar**

## Descrição do propósito do sistema
Plantera é um sistema simples de gestão escolar. Ele separa os perfis de
professores, responsáveis e administradores, permitindo consultar presenças,
conteúdo das aulas e cronograma de ensino.

## Instruções de instalação e execução

### Requisitos

- PHP 8.4 ou superior com extensão `pdo_mysql`;
- MySQL;
- Composer.

### Instalação

1. Instale as dependências:

   ```bash
   composer install
   ```

2. Crie o banco e as tabelas com o arquivo [001_create_database_schema.sql](migrations/001_create_database_schema.sql).

3. Copie `.env.example` para `.env` e informe as credenciais do MySQL.

4. Inicie o servidor local:

   ```bash
   php -S 127.0.0.1:8000 -t public
   ```

### Endpoints

O fluxo já existente de usuário permanece em `?action=...`. Os demais recursos usam `resource`:

- `GET ?resource=student&action=list`
- `POST ?resource=student&action=create`
- `GET ?resource=class&action=list`
- `POST ?resource=class&action=create`
- `GET ?resource=lesson&action=list`
- `POST ?resource=lesson&action=create`

Os endpoints aceitam também `show`, `edit` e `delete`. Turmas permitem `add-student`, `remove-student`, `add-schedule` e `remove-schedule`; aulas permitem `register-attendance`.

### Autenticação

A API usa sessão nativa do PHP. Antes do primeiro acesso, crie a conta de
administrador pelo terminal:

```bash
php scripts/create_admin.php
```

O script solicita os dados da conta e a senha é gravada com hash seguro.

- `POST ?resource=auth&action=login`: recebe `login` ou `identifier` (e-mail ou nome de usuário), além de `password`.
- `POST ?resource=auth&action=logout`: encerra a sessão atual.
- `GET ?resource=auth&action=me`: retorna os dados do usuário autenticado.

Após o login, a API devolve o usuário sem o hash da senha. A sessão mantém o ID do usuário e seu tipo somente para a navegação; as permissões da API são validadas pelo usuário consultado no banco.

## Nome dos integrantes
**Carlos Periçaro**

## Link para o DER

[Visualizar DER](BD_DER/DER.png)
