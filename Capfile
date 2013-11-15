load 'deploy'
# Uncomment if you are using Rails' asset pipeline
    # load 'deploy/assets'
load 'config/deploy' # remove this line to skip loading any of the default tasks

require 'capistrano/ext/multistage'

namespace :deploy do
  desc "Create local local_config.php in shared/config"
  task :create_settings_php, :roles => :web do
    domains.each do |domain|
        configuration = <<-EOF
<?php

  define('DB_NAME', '#{db_name}_#{stage}');

  /** MySQL database username */
  define('DB_USER', '#{db_user}');

  /** MySQL database password */
  define('DB_PASSWORD', '#{db_pass}');

  /** MySQL hostname */
  define('DB_HOST', '#{db_host}');

EOF

      put configuration, "#{deploy_to}/#{shared_dir}/#{stage}-config.php"
    end
  end

  desc "link file dirs and the local_settings.php to the shared copy"
  task :symlink_files, :roles => :web do
    domains.each do |domain|
      # link settings file
      run "ln -nfs #{deploy_to}/#{shared_dir}/#{stage}-config.php #{release_path}/#{stage}-config.php"
    end
  end
   # desc '[internal] Touches up the released code.'
    desc "Changing permissions on WP files"
    task :finalize_update, :except => { :no_release => true } do
      run "chmod -R g-w #{deploy_to}"
      run "chmod 644 #{release_path}/wp-config.php"
    end
  
  
    # Each of the following tasks are Rails specific. They're removed.
    task :migrate do
    end
  
    task :migrations do
    end
  
    task :cold do
    end
  
    task :start do
    end
  
    task :stop do
    end
  
    task :restart, :roles => :web do
    end
  
    after "deploy:setup",
      "deploy:create_settings_php"
  
    after "deploy:update_code",
      "deploy:symlink_files"
  
    after "deploy",
      "deploy:cleanup"
 end