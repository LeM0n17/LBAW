CREATE FUNCTION get_username_by_id (
    @id INT
)
RETURNS VARCHAR(20) AS
BEGIN
	DECLARE @return_value VARCHAR(20);
    SELECT @return_value = username FROM users WHERE id = @id;
    RETURN @return_value;
END;

