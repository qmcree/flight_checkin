Vagrant.configure("2") do |config|
  config.vm.box = "hashicorp/precise32"
  config.vm.provision :shell, :path => "bootstrap.sh"
  config.vm.synced_folder ".", "/vagrant", disabled: true
  config.vm.network :forwarded_port, host: 8080, guest: 80 # HTTP
  config.vm.network :forwarded_port, host: 9306, guest: 3306 # MySQL
end
