ARGS = $(filter-out $@,$(MAKECMDGOALS))

.PHONY: tests
tests:
	bin/phpunit --no-coverage ${ARGS}

coverage:
	XDEBUG_MODE=coverage  bin/phpunit ${ARGS}

qa:
	PHP_CS_FIXER_IGNORE_ENV=1 bin/qa src 2> /dev/null

major:
	git switch main
	standard-version --release-as major
	git push origin --follow-tags

minor:
	git switch main
	standard-version --release-as minor
	git push origin --follow-tags


hotfix:
	git switch main
	standard-version --release-as patch
	git push origin --follow-tags

sonar:
	bin/phpunit tests/
	sed 's:'$(PWD)'/::g' build/reports/coverage.xml > build/reports/coverage.relative.xml
	docker run --rm -ti -v $(PWD):/usr/src --link sonarqube newtmitch/sonar-scanner sonar-scanner -X \
	  -Dsonar.projectKey=planb \
	  -Dsonar.projectName=planb \
	  -Dsonar.sources=src \
	  -Dsonar.php.coverage.reportPaths=build/reports/coverage.relative.xml \
	  -Dsonar.php.tests.reportPath=build/reports/tests.xml \
	  -Dsonar.host.url=http://sonarqube:9000 \
	  -Dsonar.login=4274b727a6e13bbc44cdd637fbba892ffc5b40e1

init:
	cp -R bin/hooks/* .git/hooks/
	standard-version --release-as v0.1.0

%:      # thanks to chakrit
	@:    # thanks to William Pursell
