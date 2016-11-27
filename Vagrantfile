Vagrant.configure(2) do |config|

    NAME = `whoami | tr -d ' \n'`

    config.vm.network "forwarded_port", guest: 80, host: 8080, auto_correct: true
    config.vm.network "forwarded_port", guest: 10081, host: 10081, auto_correct: true
    config.vm.network "forwarded_port", guest: 10082, host: 10082, auto_correct: true

    config.vm.provider "virtualbox" do |vb|
        vb.customize ["modifyvm", :id, "--cpus", "2"]

        vb.customize ["modifyvm", :id, "--memory", "1024"]
    end


        config.vm.box = "ubuntu/xenial"
        config.vm.hostname = "objectiveStarter"
        config.vm.network "private_network", ip: "192.168.43.45"
        config.vm.provision "shell", path: "provision.sh"
        config.vm.box_url = "https://cloud-images.ubuntu.com/xenial/current/xenial-server-cloudimg-amd64-vagrant.box"

end