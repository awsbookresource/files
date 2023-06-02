-- Statement(1)
CREATE EXTERNAL TABLE IF NOT EXISTS temperature_data (code STRING, ymd STRING, temp FLOAT) ROW FORMAT DELIMITED FIELDS TERMINATED BY ',';


-- Statement(2)
LOAD DATA INPATH '/user/hive/input/temperature' OVERWRITE INTO TABLE temperature_data;


-- Statement(3)
SELECT code, max(temp) max_temperature FROM temperature_data GROUP BY code;

