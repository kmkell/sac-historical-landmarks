INSERT INTO `city_landmarks` (
  `objectid`, `apn`, `resource_name`, `street_address`, `ordinance`, `shape__area`, `shape__length`
)
SELECT 
  `objectid`, 
  CASE WHEN `apn` IS NULL OR TRIM(`apn`) = '' THEN 'UNKNOWN' ELSE TRIM(`apn`) END,
  CASE 
    WHEN `resource_name` IS NULL OR TRIM(`resource_name`) = '' 
    THEN CONCAT('Historic Property at ', CONCAT_WS(' ', TRIM(`house`), TRIM(`street_name`), TRIM(`street_type`)))
    ELSE TRIM(`resource_name`)
  END,
  CONCAT_WS(' ', TRIM(`house`), TRIM(`street_name`), TRIM(`street_type`)),
  `ordinance`, `shape__area`, `shape__length`
FROM `staging_historical_landmarks`;