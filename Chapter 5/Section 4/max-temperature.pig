--Load dataset
temperature_records = LOAD '/user/input/temperature' USING PigStorage(',') as (code:chararray, date:datetime, temp:float);

--Group records by airport code
group_code = GROUP temperature_records BY code;

--Calculate maximum temperature
max_temperature = FOREACH group_code GENERATE group, MAX(temperature_records.temp) as maxtemp;

--Store output
STORE max_temperature INTO 'output/pig/temp' USING PigStorage(',');
