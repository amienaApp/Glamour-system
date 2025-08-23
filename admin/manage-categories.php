the add <?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/mongodb.php';
require_once '../models/Category.php';

$categoryModel = new Category();
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_category':
            $categoryData = [
                'name' => trim($_POST['category_name'] ?? ''),
                'description' => trim($_POST['category_description'] ?? ''),
                'subcategories' => []
            ];
            
            $errors = $categoryModel->validateCategoryData($categoryData);
            if (empty($errors)) {
                $categoryId = $categoryModel->create($categoryData);
                if ($categoryId) {
                    $message = 'Category added successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to add category.';
                    $messageType = 'error';
                }
            } else {
                $message = 'Validation errors: ' . implode(', ', $errors);
                $messageType = 'error';
            }
            break;

        case 'add_subcategory':
            $categoryName = trim($_POST['parent_category'] ?? '');
            $subcategoryName = trim($_POST['subcategory_name'] ?? '');
            
            if (empty($categoryName) || empty($subcategoryName)) {
                $message = 'Please fill in all fields.';
                $messageType = 'error';
            } else {
                $success = $categoryModel->addSubcategory($categoryName, $subcategoryName);
                if ($success) {
                    $message = 'Subcategory added successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to add subcategory.';
                    $messageType = 'error';
                }
            }
            break;

        case 'delete_category':
            $categoryId = $_POST['category_id'] ?? '';
            if ($categoryModel->delete($categoryId)) {
                $message = 'Category deleted successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to delete category.';
                $messageType = 'error';
            }
            break;

        case 'edit_category':
            $categoryId = $_POST['category_id'] ?? '';
            $categoryName = trim($_POST['edit_category_name'] ?? '');
            $categoryDescription = trim($_POST['edit_category_description'] ?? '');
            $subcategoriesJson = $_POST['edit_subcategories'] ?? '[]';
            
            // Parse subcategories JSON
            $subcategories = [];
            if (!empty($subcategoriesJson)) {
                $subcategories = json_decode($subcategoriesJson, true);
                if ($subcategories === null) {
                    $subcategories = [];
                }
            }
            
            if (empty($categoryName)) {
                $message = 'Category name is required.';
                $messageType = 'error';
            } else {
                $updateData = [
                    'name' => $categoryName,
                    'description' => $categoryDescription,
                    'subcategories' => $subcategories
                ];
                
                if ($categoryModel->update($categoryId, $updateData)) {
                    $message = 'Category updated successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to update category.';
                    $messageType = 'error';
                }
            }
            break;
    }
}

$categories = $categoryModel->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Glamour Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="includes/admin-sidebar.css">
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body { 
            font-family: 'Circular Std', 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, #E3F2FD 0%, #B3E5FC 50%, #81D4FA 100%); 
            min-height: 100vh; 
            color: #3E2723; 
            display: flex;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 30px;
            width: calc(100% - 280px);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #3E2723;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .header p {
            color: #666;
            font-size: 1.1rem;
        }

        .message {
            padding: 20px 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .message.success {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
        }

        .message.error {
            background: linear-gradient(135deg, #f44336, #d32f2f);
            color: white;
            box-shadow: 0 8px 25px rgba(244, 67, 54, 0.3);
        }

        /* Form Styles */
        .form-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(62, 39, 35, 0.1);
            margin-bottom: 40px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .form-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #3E2723;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #3E2723;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #E0E0E0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #29B6F6;
            box-shadow: 0 0 0 3px rgba(41, 182, 246, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
            box-shadow: 0 5px 15px rgba(41, 182, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(41, 182, 246, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #3E2723, #5D4037);
            color: white;
            box-shadow: 0 5px 15px rgba(62, 39, 35, 0.3);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(62, 39, 35, 0.4);
        }

        /* Categories Display */
        .categories-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(62, 39, 35, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .categories-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #3E2723;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .category-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(62, 39, 35, 0.1);
            transition: all 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(62, 39, 35, 0.1);
        }

        .category-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .category-info {
            flex: 1;
        }

        .category-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #3E2723;
            margin-bottom: 8px;
        }

        .category-description {
            color: #666;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }

        .subcategories-section {
            margin-top: 10px;
        }

        .subcategories-label {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .subcategories-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .subcategory-tag {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .no-subcategories {
            color: #999;
            font-style: italic;
            font-size: 0.85rem;
        }

        .category-actions {
            flex: 0 0 120px;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            margin-top: 5px;
        }

        .btn-small {
            padding: 8px 12px;
            font-size: 0.8rem;
            border-radius: 8px;
            min-width: 50px;
        }

        .btn-edit {
            background: linear-gradient(135deg, #FF9800, #F57C00);
            color: white;
            cursor: pointer;
            pointer-events: auto;
            position: relative;
            z-index: 10;
        }

        .btn-edit:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(255, 152, 0, 0.4);
        }

        .btn-edit:active {
            transform: translateY(0);
        }

        .btn-danger {
            background: linear-gradient(135deg, #f44336, #d32f2f);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(244, 67, 54, 0.4);
        }

        /* Modal Styles */
        .modal {
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            pointer-events: auto;
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            margin: 5% auto;
            padding: 0;
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 25px 60px rgba(62, 39, 35, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .modal-header {
            padding: 25px 30px;
            border-bottom: 1px solid rgba(62, 39, 35, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            color: #3E2723;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .close {
            color: #666;
            font-size: 2rem;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #f44336;
        }

        .modal-content {
            padding: 0;
        }

        .edit-section {
            padding: 25px 30px;
            border-bottom: 1px solid rgba(62, 39, 35, 0.1);
        }

        .edit-section:last-of-type {
            border-bottom: none;
        }

        .edit-section h4 {
            color: #3E2723;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .edit-row {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .edit-row:last-child {
            margin-bottom: 0;
        }

        .edit-input-group {
            flex: 1;
        }

        .edit-input-group input,
        .edit-input-group select,
        .edit-input-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #E0E0E0;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .edit-input-group input:focus,
        .edit-input-group select:focus,
        .edit-input-group textarea:focus {
            outline: none;
            border-color: #29B6F6;
            box-shadow: 0 0 0 3px rgba(41, 182, 246, 0.1);
        }

        .edit-input-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .edit-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }

        /* Professional Action Buttons */
        .btn-rename {
            background: linear-gradient(135deg, #FF9800, #F57C00);
            color: white;
            border: none;
            position: relative;
            overflow: hidden;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.75rem;
        }

        .btn-rename:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 152, 0, 0.4);
            background: linear-gradient(135deg, #FFB74D, #FF9800);
        }

        .btn-rename:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(255, 152, 0, 0.3);
        }

        .btn-save {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            border: none;
            position: relative;
            overflow: hidden;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.75rem;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(76, 175, 80, 0.4);
            background: linear-gradient(135deg, #66BB6A, #4CAF50);
        }

        .btn-save:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        }

        .btn-add {
            background: linear-gradient(135deg, #29B6F6, #0288D1);
            color: white;
            border: none;
            position: relative;
            overflow: hidden;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.75rem;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(41, 182, 246, 0.4);
            background: linear-gradient(135deg, #4FC3F7, #29B6F6);
        }

        .btn-add:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(41, 182, 246, 0.3);
        }

        .btn-remove {
            background: linear-gradient(135deg, #f44336, #d32f2f);
            color: white;
            border: none;
            position: relative;
            overflow: hidden;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.75rem;
        }

        .btn-remove:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(244, 67, 54, 0.4);
            background: linear-gradient(135deg, #EF5350, #f44336);
        }

        .btn-remove:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);
        }

        .btn-edit {
            background: linear-gradient(135deg, #9C27B0, #7B1FA2);
            color: white;
            border: none;
            position: relative;
            overflow: hidden;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.75rem;
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(156, 39, 176, 0.4);
            background: linear-gradient(135deg, #BA68C8, #9C27B0);
        }

        .btn-edit:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(156, 39, 176, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #f44336, #d32f2f);
            color: white;
            border: none;
            position: relative;
            overflow: hidden;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.75rem;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(244, 67, 54, 0.4);
            background: linear-gradient(135deg, #EF5350, #f44336);
        }

        .btn-danger:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
            border: none;
            position: relative;
            overflow: hidden;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(33, 150, 243, 0.4);
            background: linear-gradient(135deg, #42A5F5, #2196F3);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #757575, #616161);
            color: white;
            border: none;
            position: relative;
            overflow: hidden;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(117, 117, 117, 0.4);
            background: linear-gradient(135deg, #9E9E9E, #757575);
        }

        .btn-secondary:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(117, 117, 117, 0.3);
        }

        /* Ensure buttons are clickable */
        .btn {
            cursor: pointer;
            pointer-events: auto;
            z-index: 10;
            position: relative;
        }

        .btn-add {
            cursor: pointer !important;
            pointer-events: auto !important;
            z-index: 1000 !important;
            position: relative !important;
            user-select: none !important;
        }

        .edit-actions .btn {
            cursor: pointer !important;
            pointer-events: auto !important;
            z-index: 1000 !important;
            position: relative !important;
            user-select: none !important;
        }

        .edit-actions .btn {
            margin: 0 5px;
        }

        .modal-footer {
            padding: 20px 30px;
            border-top: 1px solid rgba(62, 39, 35, 0.1);
            text-align: center;
        }

        .warning-text {
            color: #d32f2f;
            font-weight: bold;
            font-style: italic;
            margin-top: 10px;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-body p {
            margin: 10px 0;
        }

        /* Professional Responsive Design */
        @media (max-width: 1200px) {
            .container {
                padding: 20px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .categories-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 10px;
            }
            
            .header {
                padding: 20px 0;
                text-align: center;
            }
            
            .header h1 {
                font-size: 1.8rem;
                margin-bottom: 8px;
            }
            
            .header p {
                font-size: 0.9rem;
            }
            
            .form-container {
                margin-bottom: 25px;
                padding: 20px;
            }
            
            .form-title {
                font-size: 1.3rem;
                margin-bottom: 15px;
            }
            
            .form-actions {
                flex-direction: column;
                gap: 10px;
            }
            
            .form-actions .btn {
                width: 100%;
                justify-content: center;
            }
            
            .categories-container {
                gap: 15px;
            }
            
            .category-card {
                padding: 15px;
            }
            
            .category-row {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .category-actions {
                width: 100%;
                justify-content: center;
                gap: 10px;
            }
            
            .category-actions .btn {
                padding: 8px 12px;
                font-size: 0.9rem;
            }
            
            .subcategories-section {
                margin-top: 10px;
            }
            
            .subcategories-list {
                flex-wrap: wrap;
                gap: 8px;
            }
            
            .subcategory-tag {
                font-size: 0.8rem;
                padding: 4px 8px;
            }
            
            /* Modal Responsive */
            .modal-content {
                width: 95%;
                margin: 10% auto;
                max-height: 90vh;
                overflow-y: auto;
            }
            
            .modal-header {
                padding: 20px;
            }
            
            .modal-header h3 {
                font-size: 1.3rem;
            }
            
            .edit-section {
                padding: 20px;
            }
            
            .edit-section h4 {
                font-size: 1.1rem;
                margin-bottom: 15px;
            }
            
            .edit-row {
                flex-direction: column;
                gap: 10px;
                align-items: stretch;
            }
            
            .edit-actions {
                justify-content: center;
                gap: 8px;
            }
            
            .edit-actions .btn {
                padding: 8px 12px;
                font-size: 0.85rem;
            }
            
            .modal-footer {
                padding: 15px 20px;
                flex-direction: column;
                gap: 10px;
            }
            
            .modal-footer .btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 1.5rem;
            }
            
            .form-container {
                padding: 15px;
            }
            
            .form-title {
                font-size: 1.2rem;
            }
            
            .category-card {
                padding: 12px;
            }
            
            .category-name {
                font-size: 1.1rem;
            }
            
            .category-description {
                font-size: 0.85rem;
            }
            
            .category-actions .btn {
                padding: 6px 10px;
                font-size: 0.8rem;
            }
            
            .modal-content {
                width: 98%;
                margin: 5% auto;
            }
            
            .modal-header {
                padding: 15px;
            }
            
            .edit-section {
                padding: 15px;
            }
            
            .edit-actions .btn {
                padding: 6px 10px;
                font-size: 0.8rem;
            }
        }

        /* Professional Enhancements */
        .form-container {
            transition: all 0.3s ease;
        }

        .form-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(62, 39, 35, 0.15);
        }

        .category-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(62, 39, 35, 0.1);
        }

        .category-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(62, 39, 35, 0.2);
            border-color: rgba(62, 39, 35, 0.2);
        }

        .btn {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 80px;
            border-radius: 8px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .btn-small {
            padding: 8px 12px;
            font-size: 0.75rem;
            min-width: 60px;
        }

        .btn-text {
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        /* Hide button text on very small screens */
        @media (max-width: 480px) {
            .btn-text {
                display: none;
            }
            
            .btn {
                min-width: 40px;
                padding: 8px;
            }
            
            .btn-small {
                min-width: 35px;
                padding: 6px 8px;
            }
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:active {
            transform: scale(0.95);
        }

        /* Loading States */
        .btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn.loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Professional Animations */
        .category-card {
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .category-card:nth-child(1) { animation-delay: 0.1s; }
        .category-card:nth-child(2) { animation-delay: 0.2s; }
        .category-card:nth-child(3) { animation-delay: 0.3s; }
        .category-card:nth-child(4) { animation-delay: 0.4s; }
        .category-card:nth-child(5) { animation-delay: 0.5s; }
        .category-card:nth-child(6) { animation-delay: 0.6s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Enhanced Modal */
        .modal {
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            animation: slideIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Professional Scrollbar */
        .modal-content::-webkit-scrollbar {
            width: 6px;
        }

        .modal-content::-webkit-scrollbar-track {
            background: rgba(62, 39, 35, 0.1);
            border-radius: 3px;
        }

        .modal-content::-webkit-scrollbar-thumb {
            background: rgba(62, 39, 35, 0.3);
            border-radius: 3px;
        }

        .modal-content::-webkit-scrollbar-thumb:hover {
            background: rgba(62, 39, 35, 0.5);
        }
    </style>
</head>
<body>
    <?php include 'includes/admin-sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="header">
                <h1><i class="fas fa-folder-open"></i> Manage Categories</h1>
                <p>Organize your products with categories and subcategories</p>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Add Category Form -->
            <div class="form-container">
                <h2 class="form-title"><i class="fas fa-plus-circle"></i> Add New Category</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_category">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="category_name">Category Name *</label>
                            <input type="text" id="category_name" name="category_name" required placeholder="Enter category name">
                        </div>
                        <div class="form-group">
                            <label for="category_description">Description</label>
                            <textarea id="category_description" name="category_description" placeholder="Enter category description"></textarea>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Category
                        </button>
                    </div>
                </form>
            </div>

            <!-- Add Subcategory Form -->
            <div class="form-container">
                <h2 class="form-title"><i class="fas fa-folder-plus"></i> Add Subcategory</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_subcategory">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="parent_category">Parent Category *</label>
                            <select id="parent_category" name="parent_category" required>
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category['name']); ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="subcategory_name">Subcategory Name *</label>
                            <input type="text" id="subcategory_name" name="subcategory_name" required placeholder="Enter subcategory name">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-plus"></i> Add Subcategory
                        </button>
                    </div>
                </form>
            </div>

            <!-- Edit Category Modal -->
            <div id="editModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3><i class="fas fa-edit"></i> Edit Category</h3>
                        <span class="close" onclick="closeEditModal()">&times;</span>
                    </div>
                    
                    <!-- Category Edit Section -->
                    <div class="edit-section">
                        <h4><i class="fas fa-folder"></i> Edit Category</h4>
                        <div class="edit-row">
                            <div class="edit-input-group">
                                <input type="text" id="edit_category_name" placeholder="Category name">
                                <input type="hidden" id="edit_category_id">
                            </div>
                            <div class="edit-actions">
                                <button type="button" class="btn btn-small btn-rename" onclick="renameCategory()" title="Update Category Name">
                                    <i class="fas fa-edit"></i>
                                    <span class="btn-text">Update Name</span>
                                </button>
                            </div>
                        </div>
                        <div class="edit-row">
                            <div class="edit-input-group">
                                <textarea id="edit_category_description" placeholder="Category description"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Subcategories Edit Section -->
                    <div class="edit-section">
                        <h4><i class="fas fa-tags"></i> Manage Subcategories</h4>
                        <div class="edit-row">
                            <div class="edit-input-group">
                                <select id="edit_subcategory_select">
                                    <option value="">Select subcategory to edit</option>
                                </select>
                            </div>
                            <div class="edit-actions">
                                <button type="button" class="btn btn-small btn-rename" onclick="renameSubcategory()" title="Rename Subcategory">
                                    <i class="fas fa-edit"></i>
                                    <span class="btn-text">Rename</span>
                                </button>
                                <button type="button" class="btn btn-small btn-remove" onclick="removeSubcategory()" title="Remove Subcategory">
                                    <i class="fas fa-trash-alt"></i>
                                    <span class="btn-text">Remove</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Add New Subcategory -->
                        <div class="edit-row">
                            <div class="edit-input-group">
                                <input type="text" id="new_subcategory_input" placeholder="Enter new subcategory name">
                            </div>
                            <div class="edit-actions">
                                <button type="button" class="btn btn-small btn-add" onclick="addNewSubcategory();" title="Add New Subcategory">
                                    <i class="fas fa-plus"></i>
                                    <span class="btn-text">Add</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="saveAllChanges()" title="Save All Changes">
                            <i class="fas fa-save"></i>
                            <span class="btn-text">Save All Changes</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Delete Category Confirmation Modal -->
            <div id="deleteCategoryModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h3>
                        <span class="close" onclick="closeDeleteModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this category?</p>
                        <p><strong>Category:</strong> <span id="deleteCategoryName"></span></p>
                        <p class="warning-text">This action cannot be undone!</p>
                    </div>
                    <div class="modal-footer">
                        <form id="deleteCategoryForm" method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="delete_category">
                            <input type="hidden" name="category_id" id="deleteCategoryId">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete Category
                            </button>
                        </form>
                        <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Remove Subcategory Confirmation Modal -->
            <div id="removeSubcategoryModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3><i class="fas fa-exclamation-triangle"></i> Confirm Remove</h3>
                        <span class="close" onclick="closeRemoveSubcategoryModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to remove this subcategory?</p>
                        <p><strong>Subcategory:</strong> <span id="removeSubcategoryName"></span></p>
                        <p class="warning-text">This action cannot be undone!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" onclick="confirmRemoveSubcategory()">
                            <i class="fas fa-trash"></i> Remove Subcategory
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="closeRemoveSubcategoryModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Categories Display -->
            <div class="categories-container">
                <h2 class="categories-title"><i class="fas fa-list"></i> All Categories</h2>
                
                <?php if (empty($categories)): ?>
                    <div style="text-align: center; color: #666; padding: 40px;">
                        <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.5;"></i>
                        <p>No categories found. Add your first category above!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($categories as $category): ?>
                        <div class="category-card">
                            <div class="category-row">
                                <div class="category-info">
                                    <div class="category-name"><?php echo htmlspecialchars($category['name']); ?></div>
                                    <?php if (!empty($category['description'])): ?>
                                        <div class="category-description"><?php echo htmlspecialchars($category['description']); ?></div>
                                    <?php endif; ?>
                                    
                                    <div class="subcategories-section">
                                        <div class="subcategories-label">
                                            <i class="fas fa-tags"></i> Subcategories
                                        </div>
                                        <div class="subcategories-list">
                                            <?php if (!empty($category['subcategories'])): ?>
                                                <?php foreach ($category['subcategories'] as $subcategory): ?>
                                                    <span class="subcategory-tag"><?php echo htmlspecialchars($subcategory); ?></span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="no-subcategories">No subcategories</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="category-actions">
                                    <button type="button" class="btn btn-small btn-edit edit-category-btn" 
                                            data-id="<?php echo $category['_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                            data-description="<?php echo htmlspecialchars($category['description'] ?? ''); ?>"
                                            data-subcategories="<?php echo htmlspecialchars(json_encode($category['subcategories'] ?? [])); ?>"
                                            title="Edit Category">
                                        <i class="fas fa-edit"></i>
                                        <span class="btn-text">Edit</span>
                                    </button>
                                    <button type="button" class="btn btn-small btn-danger delete-category-btn"
                                            data-id="<?php echo $category['_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                            title="Delete Category">
                                        <i class="fas fa-trash-alt"></i>
                                        <span class="btn-text">Delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="includes/admin-sidebar.js"></script>
    <script>

        // Add event listeners when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Edit category buttons
            document.querySelectorAll('.edit-category-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const categoryId = this.getAttribute('data-id');
                    const categoryName = this.getAttribute('data-name');
                    const categoryDescription = this.getAttribute('data-description');
                    const subcategoriesJson = this.getAttribute('data-subcategories');
                    
            
                    editCategory(categoryId, categoryName, categoryDescription, subcategoriesJson);
                });
            });
            
            // Delete category buttons
            document.querySelectorAll('.delete-category-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const categoryId = this.getAttribute('data-id');
                    const categoryName = this.getAttribute('data-name');
                    
            
                    showDeleteCategoryModal(categoryId, categoryName);
                });
            });
        });



        let currentCategoryData = {};
        let currentSubcategories = [];

        function editCategory(categoryId, categoryName, categoryDescription, subcategoriesJson) {
    
            
            currentCategoryData = {
                id: categoryId,
                name: categoryName,
                description: categoryDescription
            };
            
            // Parse subcategories JSON
            try {
                currentSubcategories = JSON.parse(subcategoriesJson);
            } catch (e) {
                console.error('Error parsing subcategories:', e);
                currentSubcategories = [];
            }
            
            // Populate form fields
            const categoryIdInput = document.getElementById('edit_category_id');
            const categoryNameInput = document.getElementById('edit_category_name');
            const categoryDescInput = document.getElementById('edit_category_description');
            
            if (categoryIdInput) categoryIdInput.value = categoryId;
            if (categoryNameInput) categoryNameInput.value = categoryName;
            if (categoryDescInput) categoryDescInput.value = categoryDescription;
            
            // Populate subcategories dropdown
            const subcategorySelect = document.getElementById('edit_subcategory_select');

            
            if (subcategorySelect) {
                subcategorySelect.innerHTML = '<option value="">Select subcategory to edit</option>';
                
                if (currentSubcategories && currentSubcategories.length > 0) {
                    currentSubcategories.forEach((subcategory, index) => {
                        const option = document.createElement('option');
                        option.value = index;
                        option.textContent = subcategory;
                        subcategorySelect.appendChild(option);

            } else {
                console.error('Subcategory select element not found!');
            }
            
            // Show modal
            const modal = document.getElementById('editModal');
            if (modal) {
                modal.style.display = 'block';
            } else {
                console.error('Modal element not found!');
            }
        }

        function renameCategory() {
            const nameInput = document.getElementById('edit_category_name');
            const newName = nameInput.value.trim();
            
            if (newName) {
                currentCategoryData.name = newName;
                showMessage('Category name updated!', 'success');
                
                // Highlight the input to show it's been updated
                nameInput.style.borderColor = '#4CAF50';
                nameInput.style.boxShadow = '0 0 0 3px rgba(76, 175, 80, 0.1)';
                
                setTimeout(() => {
                    nameInput.style.borderColor = '#E0E0E0';
                    nameInput.style.boxShadow = 'none';
                }, 2000);
            } else {
                showMessage('Please enter a category name!', 'error');
            }
        }

        function saveCategory() {
            const nameInput = document.getElementById('edit_category_name');
            const descInput = document.getElementById('edit_category_description');
            
            const updateData = {
                name: nameInput.value.trim(),
                description: descInput.value.trim(),
                subcategories: currentSubcategories
            };
            
            // Send AJAX request to save category
            fetch('manage-categories.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'edit_category',
                    category_id: currentCategoryData.id,
                    edit_category_name: updateData.name,
                    edit_category_description: updateData.description,
                    edit_subcategories: JSON.stringify(updateData.subcategories)
                })
            })
            .then(response => response.text())
            .then(data => {
                // Close modal immediately
                closeEditModal();
                showMessage('Category saved successfully!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            })
            .catch(error => {
                showMessage('Error saving category!', 'error');
                console.error('Error:', error);
            });
        }

        function renameSubcategory() {
            const select = document.getElementById('edit_subcategory_select');
            const selectedIndex = select.value;
            
            if (selectedIndex === '') {
                showMessage('Please select a subcategory to rename!', 'error');
                return;
            }
            
            // Create inline rename input
            const currentName = select.options[select.selectedIndex].textContent;
            const renameInput = document.createElement('input');
            renameInput.type = 'text';
            renameInput.value = currentName;
            renameInput.className = 'edit-input-group';
            renameInput.style.marginTop = '10px';
            renameInput.style.padding = '8px 12px';
            renameInput.style.border = '2px solid #29B6F6';
            renameInput.style.borderRadius = '8px';
            renameInput.style.width = '100%';
            
            // Replace dropdown with input
            const subcategorySection = select.closest('.edit-row');
            const inputGroup = subcategorySection.querySelector('.edit-input-group');
            const originalSelect = inputGroup.innerHTML;
            
            inputGroup.innerHTML = '';
            inputGroup.appendChild(renameInput);
            renameInput.focus();
            renameInput.select();
            
            // Handle save on Enter key or blur
            const saveRename = () => {
                const newName = renameInput.value.trim();
                if (newName && newName !== currentName) {
                    currentSubcategories[selectedIndex] = newName;
                    
                    // Update dropdown
                    const option = select.options[select.selectedIndex];
                    option.textContent = newName;
                    
                    showMessage('Subcategory renamed!', 'success');
                }
                
                // Restore original dropdown
                inputGroup.innerHTML = originalSelect;
            };
            
            renameInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    saveRename();
                } else if (e.key === 'Escape') {
                    inputGroup.innerHTML = originalSelect;
                }
            });
            
            renameInput.addEventListener('blur', saveRename);
        }

        function removeSubcategory() {
            const select = document.getElementById('edit_subcategory_select');
            const selectedIndex = select.value;
            
            if (selectedIndex === '') {
                showMessage('Please select a subcategory to remove!', 'error');
                return;
            }
            
            const subcategoryName = select.options[select.selectedIndex].textContent;
            const subcategoryIndex = selectedIndex;
            
            // Show confirmation modal
            showRemoveSubcategoryModal(subcategoryName, subcategoryIndex);
        }

        function showRemoveSubcategoryModal(subcategoryName, subcategoryIndex) {
            document.getElementById('removeSubcategoryName').textContent = subcategoryName;
            document.getElementById('removeSubcategoryModal').setAttribute('data-index', subcategoryIndex);
            document.getElementById('removeSubcategoryModal').style.display = 'block';
        }

        function confirmRemoveSubcategory() {
            const modal = document.getElementById('removeSubcategoryModal');
            const subcategoryIndex = parseInt(modal.getAttribute('data-index'));
            const select = document.getElementById('edit_subcategory_select');
            

            
            // Remove from array immediately
            currentSubcategories.splice(subcategoryIndex, 1);
            

            
            // Update dropdown immediately
            select.innerHTML = '<option value="">Select subcategory to edit</option>';
            
            if (currentSubcategories && currentSubcategories.length > 0) {
                currentSubcategories.forEach((subcategory, index) => {
                    const option = document.createElement('option');
                    option.value = index;
                    option.textContent = subcategory;
                    select.appendChild(option);
                });
            }
            
            // Close modal immediately
            closeRemoveSubcategoryModal();
            
            // Show success message immediately
            showMessage('Subcategory removed successfully!', 'success');
            
            // Ensure buttons remain clickable
            setTimeout(ensureButtonClickability, 100);
            

        }

        function saveSubcategory() {
            const select = document.getElementById('edit_subcategory_select');
            const selectedIndex = select.value;
            
            if (selectedIndex === '') {
                showMessage('Please select a subcategory to save!', 'error');
                return;
            }
            
            // Save the current subcategories to the category
            saveCategory();
        }

        function saveAllChanges() {
            // Save all changes (category and subcategories) to the database
            saveCategory();
        }

        function addNewSubcategory() {
    
            
            // Ensure currentSubcategories is initialized
            if (!currentSubcategories) {
                currentSubcategories = [];
    
            }
            
            const input = document.getElementById('new_subcategory_input');

            
            if (!input) {
                showMessage('Input field not found!', 'error');
                return;
            }
            
            const newName = input.value.trim();

            
            if (newName) {
                // Add to current subcategories array
                currentSubcategories.push(newName);
    
                
                // Update dropdown
                const select = document.getElementById('edit_subcategory_select');
    
                
                if (select) {
                    const option = document.createElement('option');
                    option.value = currentSubcategories.length - 1;
                    option.textContent = newName;
                    select.appendChild(option);
        
                    
                    // Clear input
                    input.value = '';
                    showMessage('Subcategory added successfully!', 'success');
                    
                    // Ensure buttons remain clickable
                    setTimeout(ensureButtonClickability, 100);
                } else {
                    showMessage('Dropdown element not found!', 'error');
                }
            } else {
                showMessage('Please enter a subcategory name!', 'error');
            }
        }

        function showMessage(message, type) {
            // Create temporary message element
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.style.position = 'fixed';
            messageDiv.style.top = '20px';
            messageDiv.style.right = '20px';
            messageDiv.style.zIndex = '3000';
            messageDiv.style.padding = '15px 20px';
            messageDiv.style.borderRadius = '10px';
            messageDiv.style.color = 'white';
            messageDiv.style.fontWeight = '600';
            messageDiv.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)';
            
            if (type === 'success') {
                messageDiv.style.background = 'linear-gradient(135deg, #4CAF50, #45a049)';
            } else {
                messageDiv.style.background = 'linear-gradient(135deg, #f44336, #d32f2f)';
            }
            
            messageDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
            
            document.body.appendChild(messageDiv);
            
            // Remove after 3 seconds
            setTimeout(() => {
                messageDiv.remove();
            }, 3000);
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function showDeleteCategoryModal(categoryId, categoryName) {
            document.getElementById('deleteCategoryId').value = categoryId;
            document.getElementById('deleteCategoryName').textContent = categoryName;
            document.getElementById('deleteCategoryModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteCategoryModal').style.display = 'none';
        }

        function closeRemoveSubcategoryModal() {
            document.getElementById('removeSubcategoryModal').style.display = 'none';
        }

        // Function to ensure buttons are clickable after modal operations
        function ensureButtonClickability() {
            const buttons = document.querySelectorAll('.edit-actions .btn');
            buttons.forEach(button => {
                button.style.pointerEvents = 'auto';
                button.style.zIndex = '1000';
                button.style.position = 'relative';
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>
