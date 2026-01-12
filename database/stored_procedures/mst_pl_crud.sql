-- =============================================
-- Stored Procedures untuk CRUD tabel mst_pl
-- =============================================

-- Drop existing procedures if they exist
DROP PROCEDURE IF EXISTS sp_mst_pl_create;
DROP PROCEDURE IF EXISTS sp_mst_pl_read;
DROP PROCEDURE IF EXISTS sp_mst_pl_read_by_id;
DROP PROCEDURE IF EXISTS sp_mst_pl_update;
DROP PROCEDURE IF EXISTS sp_mst_pl_delete;
DROP PROCEDURE IF EXISTS sp_mst_pl_delete_multiple;

DELIMITER $$

-- =============================================
-- Procedure: CREATE - Menambahkan data baru
-- =============================================
CREATE PROCEDURE sp_mst_pl_create(
    IN p_id VARCHAR(50),
    IN p_desk VARCHAR(255),
    IN p_unsur VARCHAR(255),
    IN p_keterangan TEXT,
    OUT p_result_message VARCHAR(255)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_result_message = 'Error: Gagal menambahkan data';
    END;

    START TRANSACTION;

    -- Check if id already exists
    IF EXISTS (SELECT 1 FROM mst_pl WHERE id = p_id) THEN
        SET p_result_message = 'Error: ID sudah ada';
        ROLLBACK;
    ELSE
        INSERT INTO mst_pl (id, desk, unsur, keterangan, created_at, updated_at)
        VALUES (p_id, p_desk, p_unsur, p_keterangan, NOW(), NOW());
        
        SET p_result_message = 'Success: Data berhasil ditambahkan';
        COMMIT;
    END IF;
END$$

-- =============================================
-- Procedure: READ - Membaca semua data
-- =============================================
CREATE PROCEDURE sp_mst_pl_read()
BEGIN
    SELECT 
        id,
        desk,
        unsur,
        keterangan,
        created_at,
        updated_at
    FROM mst_pl
    ORDER BY id ASC;
END$$

-- =============================================
-- Procedure: READ BY ID - Membaca data berdasarkan ID
-- =============================================
CREATE PROCEDURE sp_mst_pl_read_by_id(
    IN p_id VARCHAR(50)
)
BEGIN
    SELECT 
        id,
        desk,
        unsur,
        keterangan,
        created_at,
        updated_at
    FROM mst_pl
    WHERE id = p_id;
END$$

-- =============================================
-- Procedure: UPDATE - Mengupdate data
-- =============================================
CREATE PROCEDURE sp_mst_pl_update(
    IN p_id VARCHAR(50),
    IN p_new_id VARCHAR(50),
    IN p_desk VARCHAR(255),
    IN p_unsur VARCHAR(255),
    IN p_keterangan TEXT,
    OUT p_result_message VARCHAR(255)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_result_message = 'Error: Gagal mengupdate data';
    END;

    START TRANSACTION;

    -- Check if record exists
    IF NOT EXISTS (SELECT 1 FROM mst_pl WHERE id = p_id) THEN
        SET p_result_message = 'Error: Data tidak ditemukan';
        ROLLBACK;
    -- Check if new_id already exists for another record
    ELSEIF p_id != p_new_id AND EXISTS (SELECT 1 FROM mst_pl WHERE id = p_new_id) THEN
        SET p_result_message = 'Error: ID baru sudah digunakan oleh data lain';
        ROLLBACK;
    ELSE
        UPDATE mst_pl
        SET 
            id = p_new_id,
            desk = p_desk,
            unsur = p_unsur,
            keterangan = p_keterangan,
            updated_at = NOW()
        WHERE id = p_id;
        
        SET p_result_message = 'Success: Data berhasil diupdate';
        COMMIT;
    END IF;
END$$

-- =============================================
-- Procedure: DELETE - Menghapus data berdasarkan ID
-- =============================================
CREATE PROCEDURE sp_mst_pl_delete(
    IN p_id VARCHAR(50),
    OUT p_result_message VARCHAR(255)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_result_message = 'Error: Gagal menghapus data';
    END;

    START TRANSACTION;

    -- Check if record exists
    IF NOT EXISTS (SELECT 1 FROM mst_pl WHERE id = p_id) THEN
        SET p_result_message = 'Error: Data tidak ditemukan';
        ROLLBACK;
    ELSE
        DELETE FROM mst_pl WHERE id = p_id;
        SET p_result_message = 'Success: Data berhasil dihapus';
        COMMIT;
    END IF;
END$$

-- =============================================
-- Procedure: DELETE MULTIPLE - Menghapus multiple data
-- =============================================
CREATE PROCEDURE sp_mst_pl_delete_multiple(
    IN p_ids TEXT,
    OUT p_result_message VARCHAR(255),
    OUT p_deleted_count INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_result_message = 'Error: Gagal menghapus data';
        SET p_deleted_count = 0;
    END;

    START TRANSACTION;

    -- Create temporary table to store IDs
    CREATE TEMPORARY TABLE temp_ids (id VARCHAR(50));
    
    -- Parse comma-separated IDs and insert into temp table
    SET @sql = CONCAT('INSERT INTO temp_ids VALUES (\'', REPLACE(p_ids, ',', '\'),(\''), '\')');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    -- Delete records
    DELETE m FROM mst_pl m
    INNER JOIN temp_ids t ON m.id = t.id;
    
    SET p_deleted_count = ROW_COUNT();
    SET p_result_message = CONCAT('Success: ', p_deleted_count, ' data berhasil dihapus');
    
    -- Drop temporary table
    DROP TEMPORARY TABLE temp_ids;
    
    COMMIT;
END$$

DELIMITER ;
