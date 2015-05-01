all: clean generate

clean:
	rm -Rf Snippets/AnsibleDoc/*

generate:
	./generator/snippets.php

test:
	cp -Rf `pwd` ~/Library/Application\ Support/Sublime\ Text\ 2/Packages