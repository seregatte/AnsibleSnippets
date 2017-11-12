all: clean generate

clean:
	@rm -Rf Snippets/AnsibleDoc/*

generate:
	generator/snippets.php && ansible --version

test:
	@cp -Rf `pwd` ~/Library/Application\ Support/Sublime\ Text\ 2/Packages