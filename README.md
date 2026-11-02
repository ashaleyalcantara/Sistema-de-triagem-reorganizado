# Sistema de Triagem Online para Clínicas Comunitárias

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13.8-FF2D20?logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-8-646CFF?logo=vite&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-Database-003B57?logo=sqlite&logoColor=white)

## Configuração do Ambiente

### Tecnologias utilizadas

O projeto utiliza:

- PHP 8.3;
- Laravel 13.8;
- Tailwind CSS 4;
- Vite 8;
- SQLite.

### Pré-requisitos

Para executar o projeto, é necessário ter instalado:

- PHP 8.3 ou superior;
- Composer;
- Node.js;
- NPM.

### Instalação

Clone o repositório e acesse a pasta do projeto:

```bash
git clone https://github.com/ashaleyalcantara/Sistema-de-triagem-reorganizado.git
cd Sistema-de-triagem-reorganizado
```

Instale as dependências do PHP:

```bash
composer install
```

Instale as dependências do frontend:

```bash
npm install
```

Crie o arquivo de configuração do ambiente a partir do arquivo de exemplo:

```bash
cp .env.example .env
```

Gere a chave da aplicação:

```bash
php artisan key:generate
```

O projeto utiliza SQLite como banco de dados na configuração padrão. Após configurar o ambiente, execute as migrations:

```bash
php artisan migrate
```

Para iniciar a aplicação:

```bash
php artisan serve
```

Para executar o ambiente de desenvolvimento do frontend:

```bash
npm run dev
```

---

## Estrutura do Banco de Dados

O sistema possui três tabelas principais relacionadas às funcionalidades de triagem: `funcionarios`, `pacientes` e `triagens`.

### Funcionários

A tabela `funcionarios` armazena os dados dos funcionários do sistema.

| Campo | Descrição |
|---|---|
| `id` | Identificador do funcionário. |
| `nome` | Nome do funcionário. |
| `cpf` | CPF do funcionário. |
| `email` | E-mail do funcionário. |
| `contato` | Informação de contato. |
| `senha` | Senha utilizada para autenticação. |
| `perfil` | Perfil do funcionário no sistema. |
| `foto_perfil` | Caminho da foto de perfil. |
| `created_at` | Data de criação do registro. |
| `updated_at` | Data da última atualização. |
| `deleted_at` | Data de exclusão lógica do registro. |

Os perfis disponíveis são:

- Administrador;
- Recepcionista;
- Enfermeiro;
- Médico.

### Pacientes

A tabela `pacientes` armazena os dados cadastrais dos pacientes.

| Campo | Descrição |
|---|---|
| `id` | Identificador do paciente. |
| `nome_completo` | Nome completo do paciente. |
| `cpf` | CPF do paciente. |
| `data_nascimento` | Data de nascimento. |
| `contato` | Informação de contato. |
| `nome_responsavel` | Nome do responsável, quando informado. |
| `cpf_responsavel` | CPF do responsável, quando informado. |
| `created_at` | Data de criação do registro. |
| `updated_at` | Data da última atualização. |

### Triagens

A tabela `triagens` armazena as informações relacionadas ao processo de triagem e ao atendimento dos pacientes.

| Campo | Descrição |
|---|---|
| `id` | Identificador da triagem. |
| `paciente_id` | Identificador do paciente relacionado. |
| `sintomas` | Sintomas informados durante a triagem. |
| `pressao` | Informação sobre pressão arterial. |
| `frequencia_cardiaca` | Informação sobre frequência cardíaca. |
| `temperatura` | Informação sobre temperatura. |
| `classificacao` | Nível de urgência do paciente. |
| `status` | Status do paciente no fluxo de atendimento. |
| `created_at` | Data de criação do registro. |
| `updated_at` | Data da última atualização. |

A tabela `triagens` possui um relacionamento com a tabela `pacientes`: cada registro de triagem pertence a um paciente, enquanto um paciente pode possuir vários registros de triagem.

A classificação de urgência utiliza os seguintes valores:

- `emergencia`;
- `muito_urgente`;
- `urgente`;
- `pouco_urgente`;
- `nao_urgente`.

---

## Especificação das Rotas da Aplicação

O projeto utiliza rotas web do Laravel, definidas principalmente no arquivo `routes/web.php`.

### Rotas públicas

| Método | Rota | Responsável | Descrição |
|---|---|---|---|
| GET | `/` | `TriagemController` | Exibe o painel de chamadas. |
| GET | `/login` | `AuthController` | Exibe a tela de login. |
| POST | `/login` | `AuthController` | Realiza a autenticação do funcionário. |
| POST | `/logout` | `AuthController` | Encerra a sessão do funcionário. |

### Rotas protegidas

As demais rotas estão protegidas pelo middleware `auth`.

#### Perfil e funcionários

| Método | Rota | Responsável | Descrição |
|---|---|---|---|
| GET | `/perfil` | `FuncionarioController` | Exibe o perfil do funcionário autenticado. |
| GET | `/editar` | `FuncionarioController` | Direciona para a visualização do perfil. |
| GET | `/gestaoFuncionarios` | `FuncionarioController` | Exibe os funcionários cadastrados. |
| POST | `/gestaoFuncionarios` | `FuncionarioController` | Cadastra um novo funcionário. |

#### Pacientes e triagem

| Método | Rota | Responsável | Descrição |
|---|---|---|---|
| GET | `/triagem` | `PacienteController` | Exibe informações relacionadas à fila de pacientes. |
| POST | `/pacientes` | `PacienteController` | Cadastra um paciente. |
| DELETE | `/triagem/{id}` | `PacienteController` | Remove um paciente da fila. |
| POST | `/triagem/adicionar` | `PacienteController` | Adiciona um paciente cadastrado à fila. |
| POST | `/triagem/chamar/{id}` | `TriagemController` | Chama um paciente para triagem. |
| GET | `/recepcionista` | `PacienteController` | Exibe a área da recepção. |

#### Atendimento médico

| Método | Rota | Responsável | Descrição |
|---|---|---|---|
| GET | `/medico` | `MedicoController` | Exibe a fila de atendimento médico. |
| POST | `/medico/chamar/{id}` | `MedicoController` | Chama um paciente para atendimento. |
| POST | `/medico/finalizar/{id}` | `MedicoController` | Finaliza o atendimento do paciente. |

O projeto também utiliza rotas de recursos para `funcionarios` e `triagem`.

---

## Principais Componentes do Sistema

O sistema utiliza a arquitetura MVC do Laravel. Seus principais componentes estão organizados principalmente entre os controllers e models localizados em `app`, as rotas em `routes`, as views utilizadas pela interface e os arquivos relacionados à estrutura do banco de dados em `database`.

### Controllers

Os principais controllers são:

- **AuthController:** realiza a autenticação e o encerramento da sessão dos funcionários.
- **FuncionarioController:** gerencia o perfil e o cadastro de funcionários.
- **PacienteController:** realiza o cadastro de pacientes e o gerenciamento da fila inicial.
- **TriagemController:** gerencia o processo de triagem, incluindo a chamada e a classificação dos pacientes.
- **MedicoController:** gerencia a fila e o atendimento médico.

### Models

Os principais modelos relacionados às funcionalidades do sistema são:

- **Funcionario:** representa os funcionários e é utilizado no processo de autenticação.
- **Paciente:** representa os pacientes cadastrados no sistema.
- **Triagem:** representa os registros de triagem e o fluxo de atendimento dos pacientes.

### Routes

As rotas definem os caminhos da aplicação e direcionam as requisições para os respectivos controllers. As principais rotas do sistema estão definidas no arquivo `routes/web.php`.

### Views

As interfaces da aplicação são renderizadas por meio das views do Laravel, retornadas pelos controllers conforme as funcionalidades acessadas pelo usuário.

### Database

A estrutura relacionada ao banco de dados, incluindo as migrations utilizadas para criação das tabelas, está organizada na pasta `database`.