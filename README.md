# 📊 ClassicModels Dashboard

Bem-vindo ao **ClassicModels Dashboard**, um painel de gestão dinâmico e interativo desenvolvido em **PHP + MySQL**, com autenticação baseada em sessão e operações completas de **CRUD** (Criar, Ler, Atualizar, Eliminar) sobre a base de dados `classicmodels`.

Este projeto foi desenvolvido como parte de uma **avaliação de competências** na área de desenvolvimento web, integrando boas práticas de programação e usabilidade.

---

## 🧠 Objetivo

Fornecer uma aplicação funcional e intuitiva para a gestão dos dados da base de dados `classicmodels`, permitindo:

- Listagem automática de todas as tabelas e respetivos registos  
- Visualização de comentários de tabelas e colunas  
- Edição, eliminação e inserção de registos via modal (sem recarregamento de página)  
- Gestão de utilizadores com autenticação por sessão  
- Prevenção de exclusão de registos com relações (integridade referencial)  

---

## 🛠️ Tecnologias Utilizadas

- **PHP (procedural)** – Lógica do servidor e manipulação da base de dados  
- **MySQL** – Base de dados `classicmodels`  
- **HTML + Bootstrap 5** – Interface responsiva e visual moderna  
- **Fetch API (AJAX moderno)** – Requisições assíncronas e interativas  
- **JavaScript** – Manipulação do DOM e controlo de modais  

---

## 🔐 Autenticação

O acesso ao dashboard requer login com credenciais válidas da tabela `employees`.  
A autenticação é feita através de sessão PHP, e após login o utilizador tem acesso total às funcionalidades da aplicação.

---

## 🧩 Estrutura do Projeto

```
/classicmodels-dashboard
│
├── index.php              # Página de login
├── login.php              # Lógica de autenticação
├── logout.php             # Logout e destruição da sessão
├── dashboard.php          # Painel principal (após login)
│
├── includes/
│   ├── db_connect.php     # Conexão à base de dados
│   └── db_columns.php     # Retorna metadados das colunas (JSON)
│
├── crud/
│   ├── add.php            # Inserção de novos registos
│   ├── edit.php           # Atualização de registos existentes
│   └── delete.php         # Eliminação de registos
│
└── css/
    └── style.css          # Estilos personalizados (opcional)
```

---

## ⚙️ Funcionalidades em Destaque

✅ Autenticação baseada em sessão  
✅ Leitura dinâmica de todas as tabelas da base de dados  
✅ Exibição de comentários (descritivos) das tabelas e colunas  
✅ Formulários dinâmicos com base na estrutura das tabelas  
✅ Operações CRUD completas com feedback ao utilizador  
✅ Validação para chaves primárias e proteção contra conflitos de integridade referencial  

---

## 🚀 Como Usar

1. Clona o repositório:
   ```bash
   git clone https://github.com/DanyloDuma/DanyloDuma2GI_PHP.git
   ```

2. Configura a base de dados `classicmodels` no teu servidor local (ex: XAMPP/MySQL)

3. Ajusta as credenciais no ficheiro `includes/db_connect.php` se necessário

4. Inicia o servidor local (por ex. via XAMPP ou WAMP)

5. Abre `index.php` no navegador e inicia sessão com um utilizador existente  
   (por exemplo, `employeeNumber = 1002`, `lastName = Murphy`)

---

## 💡 Nota de Segurança

Este projeto foi desenvolvido com fins educacionais e não está pronto para produção.  
Em contexto real, seria necessário:

- Usar hashing de senhas com `password_hash()`  
- Implementar melhor validação de inputs  
- Prevenir SQL Injection com mais rigor e filtros  
- Utilizar frameworks modernos como Laravel ou Symfony  
- Aplicar CSRF tokens para segurança de formulários  

---

## 👨‍💻 Autor

**Danylo Duma**  
Nº3 — 2ºGI — RC — AE Aqua Alba — 2025  
Projeto de Avaliação — PHP & MySQL

---

## 📬 Contacto

Caso tenhas dúvidas, sugestões ou queiras colaborar, sinta-te à vontade para me contactar pelo GitHub.