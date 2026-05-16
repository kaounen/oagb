# Site da Ordem dos Advogados da Guiné-Bissau (OAGB)

Site institucional completo da Ordem dos Advogados da Guiné-Bissau, desenvolvido em PHP com backend em CodeIgniter para gestão administrativa.

## 📋 Características Principais

- **Frontend**: PHP puro com design responsivo Bootstrap 5
- **Backend**: CodeIgniter 4 para área administrativa
- **Base de Dados**: MySQL com estrutura completa
- **Design**: Adaptado do template original com fontes Libre Baskerville e Open Sans
- **Funcionalidades**: 
  - Gestão de notícias com scroll infinito
  - Pesquisa de advogados
  - Sistema de inscrições e solicitações
  - Área administrativa completa (CRUD)
  - Newsletter e formulários de contacto

## 🛠️ Requisitos do Sistema

- **PHP**: 7.4 ou superior
- **MySQL**: 5.7 ou superior  
- **Apache**: com mod_rewrite habilitado
- **Extensões PHP**: PDO, mysqli, gd, curl, mbstring
- **CodeIgniter**: 4.x (para área administrativa)

## 📁 Estrutura de Diretórios

```
public_html/
├── css/                    # Estilos CSS
├── js/                     # JavaScript
├── lib/                    # Bibliotecas (Bootstrap, jQuery, etc.)
├── img/                    # Imagens
│   ├── noticias/          # Imagens das notícias
│   ├── advogados/         # Fotos dos advogados
│   ├── documentos/        # Documentos PDF
│   └── uploads/           # Uploads gerais
├── includes/              # Includes PHP (navbar, footer)
├── ajax/                  # Scripts AJAX
├── gestao/                # Área administrativa (CodeIgniter)
├── *.php                  # Páginas do site
├── connect.php            # Conexão com base de dados
├── .htaccess             # Configurações Apache
└── README.md             # Este arquivo
```

## 🚀 Instalação

### 1. Configuração da Base de Dados

1. Criar base de dados MySQL:
```sql
CREATE DATABASE oagb_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Importar estrutura da base de dados:
```bash
mysql -u username -p oagb_db < oagb.sql
```

3. Criar utilizador da base de dados:
```sql
CREATE USER 'oagb_user'@'localhost' IDENTIFIED BY 'oagb_password_2024';
GRANT ALL PRIVILEGES ON oagb_db.* TO 'oagb_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Configuração dos Ficheiros

1. **Editar `connect.php`**: Configurar credenciais da base de dados
```php
$host = 'localhost';
$dbname = 'oagb_db';
$username = 'oagb_user';
$password = 'oagb_password_2024';
```

2. **Configurar CodeIgniter** (área administrativa):
   - Editar `gestao/app/Config/Database.php`
   - Configurar `gestao/app/Config/App.php` com URL base

3. **Configurar uploads**: Criar diretórios com permissões adequadas
```bash
mkdir -p img/{noticias,advogados,documentos,uploads}
chmod 755 img/
chmod 777 img/{noticias,advogados,documentos,uploads}
```

### 3. Configuração do Servidor Web

1. **Apache Virtual Host**:
```apache
<VirtualHost *:80>
    ServerName oagb.gw
    ServerAlias www.oagb.gw
    DocumentRoot /var/www/html/oagb/public_html
    
    <Directory /var/www/html/oagb/public_html>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/oagb_error.log
    CustomLog ${APACHE_LOG_DIR}/oagb_access.log combined
</VirtualHost>
```

2. **Habilitar mod_rewrite**:
```bash
sudo a2enmod rewrite
sudo systemctl reload apache2
```

### 4. Configuração de Email

Editar as configurações de email nos ficheiros PHP:
```php
// Em connect.php
define('ADMIN_EMAIL', 'info@oagb.gw');

// Configurar SMTP nos formulários
$headers = "From: noreply@oagb.gw\r\n";
```

## 📧 Configuração de Email

Para funcionalidade completa de email, configure:

1. **Servidor SMTP** no hosting
2. **SPF Record**: `v=spf1 include:_spf.servidor.com ~all`
3. **DKIM** para autenticação
4. **DMARC** para segurança

## 🔐 Segurança

### Configurações Implementadas:
- Headers de segurança no .htaccess
- Sanitização de inputs
- Proteção contra SQL injection (PDO)
- Validação de formulários
- Proteção de ficheiros sensíveis

### Configurações Adicionais Recomendadas:
1. **SSL Certificate** (Let's Encrypt)
2. **Firewall** (mod_security)
3. **Backups** regulares da base de dados
4. **Atualizações** regulares do PHP

## 👥 Utilizadores Padrão

### Área Administrativa:
- **Utilizador**: admin
- **Email**: admin@oagb.gw  
- **Password**: (definir na instalação)

## 📱 Funcionalidades Principais

### Frontend Público:
- ✅ Página inicial com estatísticas
- ✅ Notícias com scroll infinito
- ✅ Pesquisa de advogados
- ✅ Listagem alfabética de advogados
- ✅ Formulário de solicitação de advogados
- ✅ Formulário de inscrição na Ordem
- ✅ Agenda de eventos
- ✅ Documentos públicos
- ✅ Páginas institucionais
- ✅ Newsletter e contactos

### Backend Administrativo (CodeIgniter):
- ✅ Dashboard com estatísticas
- ✅ Gestão de advogados (CRUD)
- ✅ Gestão de estagiários (CRUD)
- ✅ Gestão de notícias (CRUD)
- ✅ Gestão de solicitações
- ✅ Gestão de inscrições
- ✅ Gestão de mensagens
- ✅ Gestão de documentos
- ✅ Sistema de uploads

## 🎨 Personalização

### Fontes Utilizadas:
- **Libre Baskerville**: Títulos e headers
- **Open Sans**: Texto corrido e elementos

### Cores Principais:
- **Primária**: #007bff (azul)
- **Secundária**: #4D1C21 (vermelho escuro)
- **Texto**: #111923 (preto)
- **Muted**: #615759 (cinza)

### Alteração de Estilo:
1. Editar `css/style.css` para alterações gerais
2. Usar classes Bootstrap 5 para layout
3. Manter consistência com as fontes definidas

## 🔄 Atualizações e Manutenção

### Backups Regulares:
```bash
# Base de dados
mysqldump -u oagb_user -p oagb_db > backup_$(date +%Y%m%d).sql

# Ficheiros
tar -czf files_backup_$(date +%Y%m%d).tar.gz public_html/
```

### Logs a Monitorizar:
- Apache error logs
- PHP error logs
- Logs de formulários
- Tentativas de acesso suspeitas

## 🐛 Resolução de Problemas

### Problemas Comuns:

1. **Erro 500**: Verificar logs do Apache e permissões de ficheiros
2. **Base de dados**: Verificar credenciais em `connect.php`
3. **Uploads**: Verificar permissões das pastas `img/`
4. **Email**: Verificar configuração SMTP
5. **URLs**: Verificar configuração do mod_rewrite

### Debug Mode:
```php
// Em connect.php, adicionar para debug:
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

## 📞 Suporte

Para questões técnicas ou atualizações:
- **Email Técnico**: suporte@ada.gw
- **Documentação**: Consultar este README
- **Logs**: Verificar logs do servidor para detalhes

## 📄 Licença

Desenvolvido para a Ordem dos Advogados da Guiné-Bissau.
Template base: HTML Codex (licença open source)
Desenvolvimento: ADA - Agência de Desenvolvimento e Automação

---

**Última atualização**: Dezembro 2024
**Versão**: 1.0.0