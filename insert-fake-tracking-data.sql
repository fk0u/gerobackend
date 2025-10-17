-- ================================================================
-- INSERT FAKE DATA - phpMyAdmin Version
-- ================================================================
-- This inserts realistic test data for TRACKING table
-- Safe to run on empty or existing table
-- ================================================================

-- Delete existing test data (optional)
-- DELETE FROM trackings WHERE schedule_id IN (1, 2, 3);

-- ================================================================
-- ROUTE 1: North Jakarta (30 points)
-- ================================================================

INSERT INTO trackings (schedule_id, latitude, longitude, speed, heading, recorded_at, created_at, updated_at) VALUES
(1, -6.1988, 106.8456, 35.50, 180.00, NOW() - INTERVAL 120 MINUTE, NOW(), NOW()),
(1, -6.1978, 106.8466, 40.20, 185.50, NOW() - INTERVAL 119 MINUTE, NOW(), NOW()),
(1, -6.1968, 106.8476, 45.80, 190.30, NOW() - INTERVAL 118 MINUTE, NOW(), NOW()),
(1, -6.1958, 106.8486, 50.10, 195.20, NOW() - INTERVAL 117 MINUTE, NOW(), NOW()),
(1, -6.1948, 106.8496, 42.30, 200.10, NOW() - INTERVAL 116 MINUTE, NOW(), NOW()),
(1, -6.1938, 106.8506, 38.70, 205.00, NOW() - INTERVAL 115 MINUTE, NOW(), NOW()),
(1, -6.1928, 106.8516, 35.90, 210.50, NOW() - INTERVAL 114 MINUTE, NOW(), NOW()),
(1, -6.1918, 106.8526, 41.20, 215.20, NOW() - INTERVAL 113 MINUTE, NOW(), NOW()),
(1, -6.1908, 106.8536, 47.50, 220.10, NOW() - INTERVAL 112 MINUTE, NOW(), NOW()),
(1, -6.1898, 106.8546, 52.80, 225.30, NOW() - INTERVAL 111 MINUTE, NOW(), NOW()),
(1, -6.1888, 106.8556, 48.20, 230.00, NOW() - INTERVAL 110 MINUTE, NOW(), NOW()),
(1, -6.1878, 106.8566, 43.60, 235.50, NOW() - INTERVAL 109 MINUTE, NOW(), NOW()),
(1, -6.1868, 106.8576, 39.10, 240.20, NOW() - INTERVAL 108 MINUTE, NOW(), NOW()),
(1, -6.1858, 106.8586, 44.80, 245.10, NOW() - INTERVAL 107 MINUTE, NOW(), NOW()),
(1, -6.1848, 106.8596, 50.30, 250.00, NOW() - INTERVAL 106 MINUTE, NOW(), NOW()),
(1, -6.1838, 106.8606, 46.90, 255.30, NOW() - INTERVAL 105 MINUTE, NOW(), NOW()),
(1, -6.1828, 106.8616, 42.50, 260.20, NOW() - INTERVAL 104 MINUTE, NOW(), NOW()),
(1, -6.1818, 106.8626, 38.20, 265.10, NOW() - INTERVAL 103 MINUTE, NOW(), NOW()),
(1, -6.1808, 106.8636, 43.70, 270.00, NOW() - INTERVAL 102 MINUTE, NOW(), NOW()),
(1, -6.1798, 106.8646, 49.40, 275.50, NOW() - INTERVAL 101 MINUTE, NOW(), NOW()),
(1, -6.1788, 106.8656, 54.80, 280.20, NOW() - INTERVAL 100 MINUTE, NOW(), NOW()),
(1, -6.1778, 106.8666, 51.20, 285.10, NOW() - INTERVAL 99 MINUTE, NOW(), NOW()),
(1, -6.1768, 106.8676, 47.60, 290.00, NOW() - INTERVAL 98 MINUTE, NOW(), NOW()),
(1, -6.1758, 106.8686, 43.10, 295.30, NOW() - INTERVAL 97 MINUTE, NOW(), NOW()),
(1, -6.1748, 106.8696, 38.80, 300.20, NOW() - INTERVAL 96 MINUTE, NOW(), NOW()),
(1, -6.1738, 106.8706, 44.50, 305.10, NOW() - INTERVAL 95 MINUTE, NOW(), NOW()),
(1, -6.1728, 106.8716, 50.20, 310.00, NOW() - INTERVAL 94 MINUTE, NOW(), NOW()),
(1, -6.1718, 106.8726, 46.70, 315.50, NOW() - INTERVAL 93 MINUTE, NOW(), NOW()),
(1, -6.1708, 106.8736, 42.30, 320.20, NOW() - INTERVAL 92 MINUTE, NOW(), NOW()),
(1, -6.1698, 106.8746, 37.90, 325.10, NOW() - INTERVAL 91 MINUTE, NOW(), NOW());

-- ================================================================
-- ROUTE 2: South Jakarta (20 points)
-- ================================================================

INSERT INTO trackings (schedule_id, latitude, longitude, speed, heading, recorded_at, created_at, updated_at) VALUES
(2, -6.2288, 106.8356, 30.50, 90.00, NOW() - INTERVAL 90 MINUTE, NOW(), NOW()),
(2, -6.2278, 106.8366, 35.20, 95.50, NOW() - INTERVAL 88 MINUTE, NOW(), NOW()),
(2, -6.2268, 106.8376, 40.80, 100.30, NOW() - INTERVAL 86 MINUTE, NOW(), NOW()),
(2, -6.2258, 106.8386, 45.10, 105.20, NOW() - INTERVAL 84 MINUTE, NOW(), NOW()),
(2, -6.2248, 106.8396, 50.30, 110.10, NOW() - INTERVAL 82 MINUTE, NOW(), NOW()),
(2, -6.2238, 106.8406, 46.70, 115.00, NOW() - INTERVAL 80 MINUTE, NOW(), NOW()),
(2, -6.2228, 106.8416, 42.90, 120.50, NOW() - INTERVAL 78 MINUTE, NOW(), NOW()),
(2, -6.2218, 106.8426, 38.20, 125.20, NOW() - INTERVAL 76 MINUTE, NOW(), NOW()),
(2, -6.2208, 106.8436, 43.50, 130.10, NOW() - INTERVAL 74 MINUTE, NOW(), NOW()),
(2, -6.2198, 106.8446, 48.80, 135.30, NOW() - INTERVAL 72 MINUTE, NOW(), NOW()),
(2, -6.2188, 106.8456, 52.20, 140.00, NOW() - INTERVAL 70 MINUTE, NOW(), NOW()),
(2, -6.2178, 106.8466, 47.60, 145.50, NOW() - INTERVAL 68 MINUTE, NOW(), NOW()),
(2, -6.2168, 106.8476, 43.10, 150.20, NOW() - INTERVAL 66 MINUTE, NOW(), NOW()),
(2, -6.2158, 106.8486, 38.80, 155.10, NOW() - INTERVAL 64 MINUTE, NOW(), NOW()),
(2, -6.2148, 106.8496, 44.30, 160.00, NOW() - INTERVAL 62 MINUTE, NOW(), NOW()),
(2, -6.2138, 106.8506, 49.90, 165.30, NOW() - INTERVAL 60 MINUTE, NOW(), NOW()),
(2, -6.2128, 106.8516, 54.50, 170.20, NOW() - INTERVAL 58 MINUTE, NOW(), NOW()),
(2, -6.2118, 106.8526, 50.20, 175.10, NOW() - INTERVAL 56 MINUTE, NOW(), NOW()),
(2, -6.2108, 106.8536, 45.80, 180.00, NOW() - INTERVAL 54 MINUTE, NOW(), NOW()),
(2, -6.2098, 106.8546, 41.40, 185.50, NOW() - INTERVAL 52 MINUTE, NOW(), NOW());

-- ================================================================
-- ROUTE 3: East Jakarta (20 points)  
-- ================================================================

INSERT INTO trackings (schedule_id, latitude, longitude, speed, heading, recorded_at, created_at, updated_at) VALUES
(3, -6.2088, 106.8856, 25.50, 270.00, NOW() - INTERVAL 50 MINUTE, NOW(), NOW()),
(3, -6.2078, 106.8846, 30.20, 265.50, NOW() - INTERVAL 48 MINUTE, NOW(), NOW()),
(3, -6.2068, 106.8836, 35.80, 260.30, NOW() - INTERVAL 46 MINUTE, NOW(), NOW()),
(3, -6.2058, 106.8826, 40.10, 255.20, NOW() - INTERVAL 44 MINUTE, NOW(), NOW()),
(3, -6.2048, 106.8816, 44.30, 250.10, NOW() - INTERVAL 42 MINUTE, NOW(), NOW()),
(3, -6.2038, 106.8806, 48.70, 245.00, NOW() - INTERVAL 40 MINUTE, NOW(), NOW()),
(3, -6.2028, 106.8796, 52.90, 240.50, NOW() - INTERVAL 38 MINUTE, NOW(), NOW()),
(3, -6.2018, 106.8786, 49.20, 235.20, NOW() - INTERVAL 36 MINUTE, NOW(), NOW()),
(3, -6.2008, 106.8776, 45.50, 230.10, NOW() - INTERVAL 34 MINUTE, NOW(), NOW()),
(3, -6.1998, 106.8766, 41.80, 225.30, NOW() - INTERVAL 32 MINUTE, NOW(), NOW()),
(3, -6.1988, 106.8756, 37.20, 220.00, NOW() - INTERVAL 30 MINUTE, NOW(), NOW()),
(3, -6.1978, 106.8746, 42.60, 215.50, NOW() - INTERVAL 28 MINUTE, NOW(), NOW()),
(3, -6.1968, 106.8736, 47.10, 210.20, NOW() - INTERVAL 26 MINUTE, NOW(), NOW()),
(3, -6.1958, 106.8726, 51.80, 205.10, NOW() - INTERVAL 24 MINUTE, NOW(), NOW()),
(3, -6.1948, 106.8716, 55.30, 200.00, NOW() - INTERVAL 22 MINUTE, NOW(), NOW()),
(3, -6.1938, 106.8706, 51.90, 195.30, NOW() - INTERVAL 20 MINUTE, NOW(), NOW()),
(3, -6.1928, 106.8696, 48.50, 190.20, NOW() - INTERVAL 18 MINUTE, NOW(), NOW()),
(3, -6.1918, 106.8686, 44.20, 185.10, NOW() - INTERVAL 16 MINUTE, NOW(), NOW()),
(3, -6.1908, 106.8676, 39.80, 180.00, NOW() - INTERVAL 14 MINUTE, NOW(), NOW()),
(3, -6.1898, 106.8666, 35.40, 175.50, NOW() - INTERVAL 12 MINUTE, NOW(), NOW());

-- ================================================================
-- Verify inserted data
-- ================================================================

SELECT 
    schedule_id,
    COUNT(*) as points,
    MIN(latitude) as min_lat,
    MAX(latitude) as max_lat,
    MIN(longitude) as min_lng,
    MAX(longitude) as max_lng,
    AVG(speed) as avg_speed,
    MIN(recorded_at) as earliest,
    MAX(recorded_at) as latest
FROM trackings
GROUP BY schedule_id
ORDER BY schedule_id;

-- ================================================================
-- Expected: 70 total points (30 + 20 + 20)
-- ================================================================

SELECT 
    COUNT(*) as total_points,
    COUNT(DISTINCT schedule_id) as schedules,
    AVG(speed) as avg_speed
FROM trackings;

-- ================================================================
-- Test API after inserting:
-- curl https://gerobaks.dumeg.com/api/tracking?limit=10
-- ================================================================
