# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrant settings
VAGRANT_BASE_BOX = "wholebits/ubuntu16.10-64"
VAGRANT_MEMORY = 4096
VAGRANT_CPUS = 4
VAGRANTFILE_API_VERSION = "2"
VM_APPLICATION_ROOT = "/www"
HOSTNAME = "sogoodbb-local"
WEB_USER = "vagrant"
WEB_GROUP = "vagrant"

# Hostmanager aliases
HOST_MANAGER_ALIASES = [
  'sogoodbb.lo'
]

# Virtual machines to provision
VM_SITES = [
    {name: 'sogoodbb', provisioner: 'bootstrap.sh', primary: true}
]

# Install Required Vagrant Plugins
required_plugins = %w( vagrant-hostmanager vagrant-triggers )
required_plugins.each do |plugin|
  system "vagrant plugin install #{plugin}" unless Vagrant.has_plugin? plugin
end

# Configure Vagrant
Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.box = VAGRANT_BASE_BOX
  config.vm.hostname = HOSTNAME
  config.vm.network "private_network", type: "dhcp"
  config.vm.synced_folder '.', '/vagrant', disabled: true
  config.vm.synced_folder './provision/', '/provision', create: true

  # Host Manager Settings
  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.hostmanager.ignore_private_ip = false
  config.hostmanager.include_offline = true

  config.hostmanager.ip_resolver = proc do |vm|
    if vm.ssh_info && vm.ssh_info[:host]
      `vagrant ssh #{vm.name} -c "/sbin/ifconfig enp0s8" | grep "inet" | head -n 1 | egrep -0 "[0-9\.]+"`.strip.split(/\s/)[1]
    end
  end

  # Setup system
  config.vm.provider :virtualbox do |vb|
    vb.memory = VAGRANT_MEMORY
    vb.cpus = VAGRANT_CPUS
  end

  # Setup host
  config.vm.synced_folder ".", "#{VM_APPLICATION_ROOT}", create: true, owner: WEB_USER, group: WEB_GROUP
  config.hostmanager.aliases = ""

  HOST_MANAGER_ALIASES.each do |alias_name|
    config.hostmanager.aliases << alias_name + " "
  end

  # Provision sites
  VM_SITES.each do |vm_site|
    config.vm.define vm_site[:name], primary: vm_site[:primary] do |node|
      node.vm.provision :shell, :path => "provision/#{vm_site[:provisioner]}", :privileged => false, :args => []
    end
  end
end

