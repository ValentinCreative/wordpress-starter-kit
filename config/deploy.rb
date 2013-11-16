# Définissez le nom de votre application
set :application, "NOM_APP"
# Définissez le chemin de votre repo
set :repository,  "git@github.com:USER/REPO.git"

# Définissez le chemin vers lequel vous voulez déployer votre WP
set :deploy_to, "~/www/"

set :domains, ["default"]

set :httpd_group, 'user' # généralement votre nom d'utilisateur chez votre héberger 

set :scm, 'git'
set :deploy_via, :copy

# Définissez le domaine de votre application sur votre machine
set :local_domain, 'monapp.dev'

# Définissez vos données de connection SQL
set :db_name, 'DB_NAME'
set :db_user, 'DB_USER' 
set(:db_pass) { Capistrano::CLI.password_prompt("Mot de passe SQL : ") } #Ne touchez pas, le mot de passe vous sera demandé lors du déploiement 
set :db_host, 'DB_HOST'

# Définissez vos données de connection SQL LOCALES
set :local_db_name, 'dbname'
set :local_db_user, 'root'
set :local_db_pass, 'root'
set :local_db_host, 'localhost'

set :stages, %w(dev prod) #définissez vos branches
set :default_stage, "prod" #définissez votre branche par défaut

set :ssh_options, {:user => 'SSH_USERNAME'} # définissez votre nom d'utilisateur ssh
set :use_sudo, false

# Définissez le chemin de votre WP sur votre machine
set :app_root, "/Users/username/Sites/app"
set :local_path, "/Users/username/Sites/app" 

# Définissez l'adresse de votre serveur (ici un exemple avec AlwaysData)
role :web, "ssh.alwaysdata.com"  #HTTP server, Apache/etc
role :app, "ssh.alwaysdata.com"  # Souvent le même que web
role :db,  "ssh.alwaysdata.com", :primary => true 

# Le chemin vers WP-cli
set :wp, "/home/#{httpd_group}/.wp-cli/bin/wp" 

