# Event Manager System

## Descrição

Este sistema permite a criação, gerenciamento e participação em eventos. Os usuários podem visualizar eventos, adicionar novos eventos, acompanhar a capacidade e status dos eventos e gerenciar sua participação.

## Funcionalidades

### 1. Dashboard

- Exibe um resumo das atividades do usuário:
  - **Eventos Criados:** Quantidade total de eventos criados pelo usuário.
  - **Eventos por Status:** Número de eventos abertos, fechados e cancelados.
  - **Eventos que estou participando:** Exibe o total de eventos que o usuário está registrado.
  - Botão **Adicionar Evento** para criar novos eventos.

### 2. Adicionar Evento

- Formulário para a criação de um novo evento contendo os seguintes campos:
  - **Título**: Nome do evento.
  - **Descrição**: Detalhes do evento.
  - **Data e Hora de Início e Fim**: Período de duração do evento.
  - **Localização**: Endereço onde o evento ocorrerá.
  - **Capacidade**: Número máximo de participantes.
  - **Status**: Define se o evento está "Aberto", "Fechado" ou "Cancelado".

### 3. Listagem de Eventos

- Exibição dos eventos cadastrados no sistema com informações detalhadas:
  - **Título e descrição do evento**
  - **Data e horário**
  - **Localização**
  - **Capacidade de participantes** (inscritos/limite)
  - **Status do evento**:
    - "Aberto" (inscrições ainda disponíveis)
    - "Fechado" (evento concluído ou lotado)
    - "Cancelado"
  - **Participantes**: Lista de usuários confirmados para o evento.

## Tecnologias Utilizadas

- **Frontend:** Tailwind CSS, Alpine.js, Flowbite
- **Backend:** Laravel com Vite
- **Build Tools:** Vite, Laravel Vite Plugin
- **Estilização:** Tailwind CSS, @tailwindcss/forms, PostCSS, Autoprefixer
- **Requisições HTTP:** Axios
- **Banco de Dados:** MySQL

## Configurando o Ambiente do Projeto

### 1. Clonar o Repositório
Se ainda não clonou o projeto, utilize o comando:
```sh
git clone https://github.com/MatheusBonore/event-manager.git
cd event-manager
```

### 2. Criar o Arquivo `.env`
O Laravel utiliza um arquivo `.env` para armazenar configurações sensíveis. Se ainda não existir, copie o arquivo de exemplo:
```sh
cp .env.example .env
```

### 3. Instalar Dependências
Instale as dependências do projeto usando o Composer:
```sh
composer install
```
E as dependências do Node.js, caso necessário:
```sh
npm install
```

### 4. Gerar a Chave da Aplicação
Gere uma nova chave para garantir o funcionamento correto do Laravel:
```sh
php artisan key:generate
```

### 5. Configurar Banco de Dados
Edite o arquivo `.env` e configure as credenciais do banco de dados:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_manager
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```
Substitua `seu_usuario` e `sua_senha` pelos dados corretos do banco de dados.

### 6. Configurar E-mail
Caso utilize SMTP para envio de e-mails, preencha com os dados corretos:
```ini
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu_email@gmail.com
MAIL_PASSWORD="sua_senha"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="seu_email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 7. Rodar as Migrations e Seeders
Para criar as tabelas no banco de dados e popular com dados de teste, execute:
```sh
php artisan migrate:fresh --seed
```

### 8. Servir a Aplicação
Agora, inicie o servidor local com:
```sh
php artisan serve
```
A aplicação estará disponível em: [http://127.0.0.1:8000](http://127.0.0.1:8000)

### 9. Compilar os Assets (Opcional)
Caso o projeto utilize frontend com Vite, rode:
```sh
npm run dev
```
Agora seu ambiente está pronto para uso!

---

## Documentação da API

### Base URL
A API está disponível localmente no seguinte endereço:
```
http://127.0.0.1:8000
```

### Autenticação
A API utiliza autenticação baseada em tokens (Bearer Token). Para acessar as rotas protegidas, é necessário primeiro se registrar e fazer login para obter um token de autenticação.

#### Criar um usuário (Registro)
```
POST /api/register
```
**Parâmetros:**
- `name`: Nome do usuário
- `email`: E-mail do usuário
- `password`: Senha do usuário
- `password_confirmation`: Confirmação da senha

**cURL Example:**
```sh
curl --location 'http://127.0.0.1:8000/api/register' \
--header 'Accept: application/json' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'name=Test System' \
--data-urlencode 'email=test@email.com' \
--data-urlencode 'password=root@eventmanager' \
--data-urlencode 'password_confirmation=root@eventmanager'
```

#### Login
```
POST /api/login
```
**Parâmetros:**
- `email`: E-mail do usuário
- `password`: Senha do usuário

**cURL Example:**
```sh
curl --location 'http://127.0.0.1:8000/api/login' \
--header 'Accept: application/json' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'email=seu_email@gmail.com' \
--data-urlencode 'password=root@eventmanager'
```

#### Logout
```
POST /api/logout
```
**cURL Example:**
```sh
curl --location --request POST 'http://127.0.0.1:8000/api/logout' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer <TOKEN>'
```

#### Obter Usuário Autenticado
```
GET /api/user
```
**cURL Example:**
```sh
curl --location 'http://127.0.0.1:8000/api/user' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer <TOKEN>'
```

---

## Gerenciamento de Eventos

#### Listar Eventos
```
GET /api/events?include=creator,attendees
```
**cURL Example:**
```sh
curl --location 'http://127.0.0.1:8000/api/events?include=creator%2Cattendees' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer <TOKEN>'
```

#### Obter um Evento Específico
```
GET /api/events/{event_id}?include=creator,attendees
```
**cURL Example:**
```sh
curl --location 'http://127.0.0.1:8000/api/events/{event_id}?include=creator%2Cattendees' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer <TOKEN>'
```

#### Criar um Evento
```
POST /api/events
```
**Parâmetros:**
- `title`: Título do evento
- `description`: Descrição do evento
- `location`: Localização do evento
- `capacity`: Capacidade máxima de participantes
- `status`: Estado do evento (ex: "open")
- `start_time`: Data e hora de início
- `end_time`: Data e hora de término

**cURL Example:**
```sh
curl --location 'http://127.0.0.1:8000/api/events' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer <TOKEN>' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'title=Test' \
--data-urlencode 'description=Testando' \
--data-urlencode 'location=Marília' \
--data-urlencode 'capacity=200' \
--data-urlencode 'status=open' \
--data-urlencode 'start_time=2025-03-28 11:42:33' \
--data-urlencode 'end_time=2025-03-28 15:42:33'
```

#### Atualizar um Evento
```
PUT /api/events/{event_id}
```
**Parâmetros:**
- `title`: Novo título do evento
- `description`: Nova descrição do evento
- `location`: Nova localização
- `capacity`: Nova capacidade máxima
- `status`: Novo status do evento
- `start_time`: Nova data e hora de início
- `end_time`: Nova data e hora de término

**cURL Example:**
```sh
curl --location --request PUT 'http://127.0.0.1:8000/api/events/{event_id}' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer <TOKEN>' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'title=Novo Título' \
--data-urlencode 'description=Nova Descrição'
```
