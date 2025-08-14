<!DOCTYPE html>
<html>
<head>
    <title>Test Subcategory AJAX</title>
</head>
<body>
    <h1>Test Subcategory AJAX Loading</h1>
    
    <div>
        <label for="category">Category:</label>
        <select id="category" onchange="loadSubcategories()">
            <option value="">Select Category</option>
            <option value="Women's Clothing">Women's Clothing</option>
            <option value="Men's Clothing">Men's Clothing</option>
            <option value="Accessories">Accessories</option>
        </select>
    </div>
    
    <div>
        <label for="subcategory">Subcategory:</label>
        <select id="subcategory">
            <option value="">Select Subcategory</option>
        </select>
    </div>
    
    <div id="debug"></div>

    <script>
        function loadSubcategories() {
            const categorySelect = document.getElementById('category');
            const subcategorySelect = document.getElementById('subcategory');
            const debugDiv = document.getElementById('debug');
            const category = categorySelect.value;

            subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
            debugDiv.innerHTML = 'Loading...';
            
            if (!category) {
                debugDiv.innerHTML = 'No category selected';
                return;
            }
                        
            fetch(`get-subcategories.php?category=${encodeURIComponent(category)}`)
                .then(response => response.json())
                .then(data => {
                    debugDiv.innerHTML = '<strong>Response:</strong> ' + JSON.stringify(data, null, 2);
                    
                    const subcategories = data.subcategories || data;
                    if (subcategories && subcategories.length > 0) {
                        subcategories.forEach(sub => {
                            const option = document.createElement('option');
                            // Handle both string and object formats
                            const subName = typeof sub === 'string' ? sub : sub.name;
                            option.value = subName;
                            option.textContent = subName;
                            subcategorySelect.appendChild(option);
                        });
                        debugDiv.innerHTML += '<br><strong>Loaded ' + subcategories.length + ' subcategories</strong>';
                    } else {
                        debugDiv.innerHTML += '<br><strong>No subcategories found</strong>';
                    }
                })
                .catch(error => {
                    console.error('Error loading subcategories:', error);
                    debugDiv.innerHTML = '<strong>Error:</strong> ' + error.message;
                });
        }
    </script>
</body>
</html>



