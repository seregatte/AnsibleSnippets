#!/bin/bash
sudo apt-get update > /dev/null
sudo apt-get install git-core --no-install-recommends --no-install-suggests -y
git config --global user.email "seregatte@gmail.com"
git config --global user.name "João Paulo Seregatte Costa"
git fetch --all
docker build generator/ -t builder:latest
export ANSIBLE_VERSION=`docker run --rm -it builder:latest ansible --version | head -1 | cut -d' ' -f2`
export CI_BRANCH=`date +%Y-%m-%d`-ci
export CI_NEXT_TAG=$(printf '%s.%0d' `git tag -l | tail -1 | cut -d'.' -f1-2` $((`git tag -l | tail -1 | cut -d'.' -f3`+1)))
git checkout -b ${CI_BRANCH} || git checkout ${CI_BRANCH}
git pull --rebase origin ${CI_BRANCH} && true
docker run --rm -it -v `pwd`:/var/www/html builder:latest make
git add . *.sublime-snippet
mv README.md README.md.tpl; cat README.md.tpl | sed -E "s%(Ansible\ Snippets).*%\1 ${CI_NEXT_TAG}%" > README.md ; rm README.md.tpl
git add README.md
git commit -am "Travis build: ${TRAVIS_BUILD_NUMBER} for ${ANSIBLE_VERSION}"
git remote add origin-ci https://${GITHUB_TOKEN}@github.com/seregatte/AnsibleSnippets.git > /dev/null 2>&1
git push --quiet --set-upstream origin-ci ${CI_BRANCH}