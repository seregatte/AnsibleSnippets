all: clean generate

clean:
	@rm -Rf Snippets/AnsibleDoc/*

generate:
	@docker run --rm -it -v `pwd`:/workspace ubuntu bash -c 'DEBIAN_FRONTEND=noninteractive && apt-get update && apt-get install -y -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" software-properties-common python3 && apt-add-repository -y ppa:ansible/ansible && apt-get -y -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" install ansible less php-cli && cd /workspace && ./generator/snippets.php && ansible --version'

test:
	@cp -Rf `pwd` ~/Library/Application\ Support/Sublime\ Text\ 2/Packages