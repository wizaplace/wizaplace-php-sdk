VAGRANTFILE_API_VERSION = '2'

Vagrant.require_version ">= 1.8.0"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    # Box
    config.vm.box = "kuikui/modern-lamp"
    config.vm.box_version = ">= 3.0.3"

    config.vm.provider "virtualbox" do |v|
      v.memory = 2048
      v.customize ['modifyvm', :id, '--cableconnected1', 'on']
    end

    config.vm.hostname = 'wizaplace-sdk.loc'

    # Network
    config.vm.network 'private_network', type: 'dhcp'
    # Necessaire sous windows : à décommenter
    # config.vm.network 'forwarded_port', guest: 80, host: 8888

    # SSH
    config.ssh.forward_agent = true

    # Folders
    # A commenter sous windows (le partage par defaut est deja au bon endroit donc pas besoin de le preciser
    # et les mount_options definies ci-dessous sont incompatibles)
    config.vm.synced_folder '.', '/vagrant', type: 'nfs', mount_options: ['nolock', 'actimeo=1', 'fsc']

    if Vagrant.has_plugin?("vagrant-cachier")
        config.cache.scope = :box
        config.cache.enable :composer
        config.cache.enable :npm
        config.cache.synced_folder_opts = {
          type: :nfs,
          mount_options: ['nolock', 'actimeo=1', 'fsc']
        }
    end
end

local_vagrantfile = File.expand_path('../Vagrantfile.local', __FILE__)
load local_vagrantfile if File.exists?(local_vagrantfile)
