-- Riley medication/allergy log table
CREATE TABLE IF NOT EXISTS `riley` (
  `id`          int           NOT NULL AUTO_INCREMENT,
  `datetime`    datetime      NOT NULL,
  `bglevel`     int           DEFAULT NULL,
  `insulin`     decimal(10,1) DEFAULT NULL,
  `gabapentin`  decimal(10,1) DEFAULT NULL,
  `allergy`     decimal(10,1) DEFAULT NULL,
  `inhaler`     int           DEFAULT NULL,
  `note`        text          DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
