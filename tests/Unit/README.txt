IMPORTANT:

Before running unit tests please change the database target to a test database,
phpunit tests are not intended to run on a live database as they will add and delete
records from tables, it may also cause some tests to fail.

To migrate the database please refer to the .env file in the root directory.
If you are still unsure Johnny has provided instructions on how to do so on the 
README file on github inside the root directory of the project.

To change the database target for testing add a line to the phpunit.xml file
inside of the <php> flags, <env name="DB_DATABASE" value="{TEST_DABATASE_TARGET}"/> 
replacing {TEST_DATABASE_TARGET} with the name of the test database