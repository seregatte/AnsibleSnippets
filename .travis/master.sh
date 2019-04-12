#!/bin/bash
sudo apt-get update > /dev/null
sudo apt-get install git-core --no-install-recommends --no-install-suggests -y
git config --global user.email "seregatte@gmail.com"
git config --global user.name "João Paulo Seregatte Costa"
git fetch --tags --force
export CI_NEXT_TAG=$(printf '%s.%0d' `git tag -l | tail -1 | cut -d'.' -f1-2` $((`git tag -l | tail -1 | cut -d'.' -f3`+1)))
git tag -a ${CI_NEXT_TAG} -m "${TRAVIS_COMMIT_MESSAGE}"
git remote add origin-ci https://${GITHUB_TOKEN}@github.com/seregatte/AnsibleSnippets.git > /dev/null 2>&1
git push --quiet --set-upstream origin-ci --tags