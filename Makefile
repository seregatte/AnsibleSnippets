all: clean generate

clean:
	@rm -Rf Snippets/AnsibleDoc/*

generate:
	./generator/modules.php | xargs -L1 -n1 -P8 php ./generator/snippets.php && ansible --version

test:
	@cp -Rf `pwd` ~/Library/Application\ Support/Sublime\ Text\ 2/Packages