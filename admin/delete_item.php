<?php
require 'connect.php';

if (isset($_GET['id'])) {
    $item_id = intval($_GET['id']);

    // Get image file path to delete from folder (optional but recommended)
    $getImage = $conn->prepare("SELECT image FROM items WHERE item_id = ?");
    $getImage->bind_param("i", $item_id);
    $getImage->execute();
    $result = $getImage->get_result();
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        $image_path = $item['image'];

        // Delete image from folder if needed
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // Delete from database
    $stmt = $conn->prepare("DELETE FROM items WHERE item_id = ?");
    $stmt->bind_param("i", $item_id);

    if ($stmt->execute()) {
        echo "<script>alert('Item deleted successfully'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to delete item'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid request'); window.history.back();</script>";
}
?>
