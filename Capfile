load 'deploy'
# Uncomment if you are using Rails' asset pipeline
    # load 'deploy/assets'
Dir['vendor/plugins/*/recipes/*.rb'].each { |plugin| load(plugin) }
load 'config/deploy' # remove this line to skip loading any of the default tasks

require 'capistrano/ext/multistage'

namespace :deploy do
	desc "Prepares one or more servers for deployment."
	task :setup, :roles => :web, :except => { :no_release => true } do
 		dirs = [deploy_to, releases_path, shared_path]
  		domains.each do |domain|
    		dirs += [shared_path + "/content"]
    		dirs += [shared_path + "/content/avatars"]
    		dirs += [shared_path + "/content/uploads"]
    		dirs += [shared_path + "/content/w3tc"]
    		dirs += [shared_path + "/content/cache"]
  		end
  		dirs += %w(system).map { |d| File.join(shared_path, d) }
  		run "mkdir -m 0775 -p #{dirs.join(' ')}"
  		# add setgid bit, so that files/ contents are always in the httpd group
  		run "chmod 2775 #{shared_path}/content"
  		run "chmod 2775 #{shared_path}/content/avatars"
		run "chmod 2775 #{shared_path}/content/uploads"
		run "chmod 2775 #{shared_path}/content/w3tc"
		run "chmod 2775 #{shared_path}/content/cache"
		run "chgrp #{httpd_group} #{shared_path}/content/"
		run "chgrp #{httpd_group} #{shared_path}/content/*"
	end
  	
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
			run "ln -nfs #{deploy_to}/#{shared_dir}/content/uploads #{release_path}/content/uploads"
			run "ln -nfs #{deploy_to}/#{shared_dir}/content/w3tc #{release_path}/content/w3tc"
			run "ln -nfs #{deploy_to}/#{shared_dir}/content/avatars #{release_path}/content/avatars"
			run "ln -nfs #{deploy_to}/#{shared_dir}/content/cache #{release_path}/content/cache"
		end
	end
	
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
 
namespace :db do
	desc "Download a backup of the database(s) from the given stage."
	task :down, :roles => :db, :only => { :primary => true } do
		domains.each do |domain|
			filename = "down_#{stage}.sql"
			temp = "/tmp/#{release_name}_#{application}_#{filename}"
			run "touch #{temp} && chmod 600 #{temp}"
			run_locally "mkdir -p db"
			run "cd #{deploy_to}/current/wp && #{wp} db export #{temp} && cd -"
			download("#{temp}", "db/#{filename}", :via=> :scp)
			if "#{stage}" == "prod"
				search = "domain.com" #FIXME
			else
				search = "#{stage}.domain.com" #FIXME
			end
			replace = local_db_host
			puts "searching (#{search}) and replacing (#{replace}) domain information"
			run_locally "sed -e 's/#{search}/#{replace}/g' -i .bak db/#{filename}"
			run "rm #{temp}"
		end
	end
 
	desc "Download and apply a backup of the database(s) from the given stage."
	task :pull, :roles => :db, :only => { :primary => true } do
		domains.each do |domain|
			filename = "pull_#{stage}.sql"
			system "cd #{app_root} ; #{wp} db import #{filename}"
		end
	end
 
	desc "Upload database(s) to the given stage."
	task :push, :roles => :db, :only => { :primary => true } do
		domains.each do |domain|
			filename = "push_#{stage}.sql"
			run_locally "cd wp && wp db export ../db/#{filename}"
			temp = "/tmp/#{release_name}_#{application}_#{filename}"
			run "touch #{temp} && chmod 600 #{temp}"
			if "#{stage}" == "prod"
				replace = "domain.com" #FIXME
			else
				replace = "#{stage}.domain.com" #FIXME
			end
			search = local_domain
			puts "searching (#{search}) and replacing (#{replace}) domain information"
			run_locally "sed -e 's/#{search}/#{replace}/g' -i .bak db/#{filename}"
			upload("db/#{filename}", "#{temp}", :via=> :scp)
			run "cd #{current_path}/wp && #{wp} db import #{temp}"
			run "rm #{temp}"
		end
	end
 
	before "db:pull", 
		"db:down"
   
	desc "Clean db directory"
	task :clean,  :roles => :db, :only => { :primary => true } do
		domains.each do |domain|
			run "rm -rf #{current_path}/db && mkdir #{current_path}/db"
			run_locally "rm -rf db && mkdir db"
		end
	end
   
	after "db:push",
		"db:clean"
		
end
 
namespace :files do
  desc "Download a backup of the wp-content (minus themes + plugins) directory from the given stage."
  task :pull, :roles => :web do
    domains.each do |domain|
      if exists?(:gateway)
        run_locally("rsync --recursive --times --omit-dir-times --chmod=ugo=rwX --rsh='ssh #{ssh_options[:user]}@#{gateway} ssh  #{ssh_options[:user]}@#{find_servers(:roles => :web).first.host}' --compress --human-readable --progress --exclude 'content/plugins' --exclude 'content/themes' :#{deploy_to}/#{shared_dir}/content/ content/")
      else
        run_locally("rsync --recursive --times --omit-dir-times --chmod=ugo=rwX --rsh=ssh --compress --human-readable --progress --exclude 'content/plugins' --exclude 'content/themes' #{ssh_options[:user]}@#{find_servers(:roles => :web).first.host}:#{deploy_to}/#{shared_dir}/content/ content/")
      end
    end
  end

  desc "Push a backup of the wp-content (minus themes + plugins) directory from the given stage."
  task :push, :roles => :web do
    domains.each do |domain|
      if exists?(:gateway)
        run_locally("rsync --recursive --times --omit-dir-times --chmod=ugo=rwX --rsh='ssh #{ssh_options[:user]}@#{gateway} ssh  #{ssh_options[:user]}@#{find_servers(:roles => :web).first.host}' --compress --human-readable --progress --exclude 'content/plugins' --exclude 'content/themes' content/ :#{deploy_to}/#{shared_dir}/content/")
      else
        run_locally("rsync --recursive --times --omit-dir-times --chmod=ugo=rwX --rsh=ssh --compress --human-readable --progress --exclude 'content/plugins' --exclude 'content/themes' content/ #{ssh_options[:user]}@#{find_servers(:roles => :web).first.host}:#{deploy_to}/#{shared_dir}/content/")
      end
    end
  end
end
 
 