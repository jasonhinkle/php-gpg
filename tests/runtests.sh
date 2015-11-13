#
# PHPUNIT TEST RUNNER
#
clear

echo "Cleaning folders..."
rm -rf ./coverage 2> /dev/null
mkdir coverage

phpunit --coverage-html ./coverage gpg/

echo "Testing Complete."
