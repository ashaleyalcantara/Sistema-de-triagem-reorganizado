# Sistema de Triagem Online para Clínicas Comunitárias

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13.8-FF2D20?logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-8-646CFF?logo=vite&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-Database-003B57?logo=sqlite&logoColor=white)

## Arquitetura

O projeto segue a divisão entre backend e frontend dentro do Laravel:

- **Backend**
  - `app/Http/Controllers`: controllers finos, responsáveis apenas por orquestrar a requisição e a resposta.
  - `app/Http/Requests`: validação de formulários.
  - `app/Services`: regras de negócio da triagem, fila, pacientes e funcionários.
  - `app/Policies` e `app/Http/Middleware`: autorização por perfil.
  - `app/Enums`: perfis, status de triagem e classificações de risco.
  - `routes/web.php`: rotas web agrupadas por perfil de acesso.
- **Frontend**
  - `resources/views/layouts`: layouts base das páginas autenticadas e convidados.
  - `resources/views/components`: header, mensagens de feedback e componentes visuais reutilizáveis.
  - `resources/views`: páginas e parciais Blade.
  - `resources/css` e `resources/js`: estilos e scripts compilados pelo Vite, sem dependência de CDNs de Tailwind/Lucide/Alpine.

O fluxo esperado é: rota → controller → service → model, com as views cuidando apenas da apresentação.

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

- Laragon (Apache + PHP 8.3);
- Composer;
- Node.js;
- NPM.

O projeto foi desenvolvido e testado no Laragon, no Windows, com PHP 8.3 e SQLite.

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

> No Laragon, se a instalação estiver muito lenta, habilite `extension=zip` no `php.ini`. Sem essa extensão, o Composer tende a baixar alguns pacotes via source.

Crie o arquivo de configuração do ambiente a partir do arquivo de exemplo:

```bash
cp .env.example .env
```

Gere a chave da aplicação:

```bash
php artisan key:generate
```

O projeto utiliza SQLite como banco de dados na configuração padrão (`DB_CONNECTION=sqlite`). Crie o arquivo do banco:

```bash
touch database/database.sqlite
```

Execute as migrations:

```bash
php artisan migrate
```

Popule o banco com os dados de teste. O `DatabaseSeeder` chama o `HospitalSeeder`, que cria funcionários e pacientes de exemplo:

```bash
php artisan db:seed
```

Se preferir executar apenas o seeder do hospital:

```bash
php artisan db:seed --class=HospitalSeeder
```

Crie o link simbólico do storage, necessário para as fotos de perfil:

```bash
php artisan storage:link
```

Instale as dependências do frontend e gere os assets de produção:

```bash
npm install
npm run build
```

> `npm run dev` é opcional e serve apenas para desenvolvimento com recarregamento automático. Para o uso normal no Laragon, utilize `npm run build`.

### Execução no Laragon

O vhost do Laragon deve apontar o **DocumentRoot** para a pasta `public` do projeto. A configuração já criada fica em:

```text
C:\laragon\etc\apache2\sites-enabled\auto.Sistema-de-Triagem.test.conf
```

Com isso, a aplicação fica disponível em:

```text
http://Sistema-de-Triagem.test
```

Se o endereço não resolver, clique em **Menu > Reload** no Laragon para regenerar o vhost e o arquivo `hosts`. Como alternativa, adicione manualmente a entrada abaixo em `C:\Windows\System32\drivers\etc\hosts` (abrindo o editor como administrador):

```text
127.0.0.1 Sistema-de-Triagem.test
```

Também é possível executar a aplicação sem o vhost, usando o servidor embutido do Laravel:

```bash
php artisan serve
```

### Usuários de teste

O `HospitalSeeder` cria os seguintes usuários. A senha é a mesma para todos: `senha123`.

| E-mail | Perfil |
|---|---|
| `administrador@tria.com` | Administrador |
| `recepcao@tria.com` | Recepcionista |
| `enfermeiro@tria.com` | Enfermeiro |
| `medico@tria.com` | Médico |

> No formulário de login é obrigatório selecionar o perfil correspondente ao usuário. Os valores enviados pelo formulário são minúsculos: `administrador`, `recepcionista`, `enfermeiro` e `medico`.

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
| GET | `/perfil` | `FuncionarioController` | Exibe o perfil do funcionário autenticado (rota nomeada `funcionarios.perfil`). |
| GET | `/editar` | `FuncionarioController` | Direciona para a visualização do perfil (rota nomeada `funcionarios.editar`). |
| GET | `/gestaoFuncionarios` | `FuncionarioController` | Exibe os funcionários cadastrados (rota nomeada `admin`). |
| POST | `/gestaoFuncionarios` | `FuncionarioController` | Cadastra um novo funcionário. |

Rotas de recurso de `funcionarios`:

| Método | Rota | Responsável | Descrição |
|---|---|---|---|
| GET | `/funcionarios` | `FuncionarioController@index` | Lista os funcionários. |
| POST | `/funcionarios` | `FuncionarioController@store` | Cadastra um funcionário. |
| GET | `/funcionarios/{id}/edit` | `FuncionarioController@edit` | Exibe o formulário de edição do funcionário. |
| PUT/PATCH | `/funcionarios/{id}` | `FuncionarioController@update` | Atualiza os dados do funcionário. |

As rotas `create`, `show` e `destroy` de `funcionarios` não existem.

#### Pacientes e triagem

| Método | Rota | Responsável | Descrição |
|---|---|---|---|
| GET | `/triagem` | `PacienteController` | Exibe informações relacionadas à fila de pacientes. |
| POST | `/pacientes` | `PacienteController` | Cadastra um paciente. |
| DELETE | `/triagem/{id}` | `PacienteController@destroy` | Remove um paciente da fila. |
| POST | `/triagem/adicionar` | `PacienteController` | Adiciona um paciente cadastrado à fila. |
| POST | `/triagem/chamar/{id}` | `TriagemController` | Chama um paciente para triagem. |
| GET | `/recepcionista` | `PacienteController` | Exibe a área da recepção. |

A rota de recurso de `triagem` possui apenas o `store`:

| Método | Rota | Responsável | Descrição |
|---|---|---|---|
| POST | `/triagem` | `TriagemController@store` | Registra a triagem de um paciente. |

As rotas `create`, `show`, `edit`, `update` e `destroy` do recurso `triagem` não existem. A remoção de um paciente da fila é feita por `DELETE /triagem/{id}`, tratada por `PacienteController@destroy`.

#### Atendimento médico

| Método | Rota | Responsável | Descrição |
|---|---|---|---|
| GET | `/medico` | `MedicoController` | Exibe a fila de atendimento médico. |
| POST | `/medico/chamar/{id}` | `MedicoController` | Chama um paciente para atendimento. |
| POST | `/medico/finalizar/{id}` | `MedicoController` | Finaliza o atendimento do paciente. |

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