<?php
// --- STEP 0: SETUP AND DEBUGGING ---

// Force PHP to display all errors. This is essential for debugging.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include your standard header file
include_once('include/header.php');

// Include your database connection
include('include/config.php'); // Make sure this path is correct

// CRITICAL: Check if the database connection was successful
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}



// --- STEP 1: GET USER INPUT ---

// Get the selected category ID from the URL. Default to '2' if not set.
$selected_cat_id = isset($_GET['category']) ? (int)$_GET['category'] : 2;

// Get the search term from the URL. Default to an empty string.
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';


// --- STEP 2: FETCH DATA FOR SIDEBAR ---

$categories_sql = "
    SELECT 
        c.category_id, 
        c.category_name, 
        COUNT(p.publication_id) as publication_count
    FROM categories c
    LEFT JOIN publications p ON c.category_id = p.category_id
    GROUP BY c.category_id, c.category_name
    ORDER BY c.category_name ASC
";
$categories_result = mysqli_query($con, $categories_sql);
// Check if the categories query failed
if (!$categories_result) {
    die("Error fetching categories: " . mysqli_error($con));
}


// --- STEP 3: FETCH MAIN PUBLICATION DATA (SECURELY) ---

$publications_sql = "
    SELECT
        p.publication_id, p.title, p.conference_details, p.publication_date, c.category_name,
        GROUP_CONCAT(DISTINCT a.author_name ORDER BY a.author_name SEPARATOR ', ') AS authors
    FROM publications p
    JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN publication_authors pa ON p.publication_id = pa.publication_id
    LEFT JOIN authors a ON pa.author_id = a.author_id
";

// Use an array to build the WHERE clause securely
$conditions = [];
$params = [];
$types = '';

// Add category condition
$conditions[] = "p.category_id = ?";
$types .= 'i';
$params[] = $selected_cat_id;

// Add search condition ONLY if a search term was provided
if (!empty($search_term)) {
    // This condition checks title, details, AND author names
    $conditions[] = "(p.title LIKE ? OR p.conference_details LIKE ? OR a.author_name LIKE ?)";
    $search_like = '%' . $search_term . '%';
    $types .= 'sss';
    $params[] = $search_like;
    $params[] = $search_like;
    $params[] = $search_like;
}

// Append the conditions to the main SQL query if any exist
if (!empty($conditions)) {
    $publications_sql .= " WHERE " . implode(' AND ', $conditions);
}

$publications_sql .= " GROUP BY p.publication_id ORDER BY p.publication_date DESC";

// Prepare and execute the statement
$stmt = mysqli_prepare($con, $publications_sql);

if ($stmt) {
    // ✅ CORRECTION: Use call_user_func_array for better PHP version compatibility
    if (!empty($params)) {
        $refs = array();
        $refs[] = $stmt;
        $refs[] = $types;
        for ($i = 0; $i < count($params); $i++) {
            $refs[] = &$params[$i]; // Bind parameters by reference
        }
        call_user_func_array('mysqli_stmt_bind_param', $refs);
    }
    
    mysqli_stmt_execute($stmt);
    $publications_result = mysqli_stmt_get_result($stmt);
} else {
    // Handle SQL error if preparation fails
    die("Error preparing statement: " . mysqli_error($con));
}

?>

<style>
    /* Custom radio button styles for a cleaner look */
    .custom-radio:checked {
        border-color: #007bff;
    }
    .custom-radio:checked::before {
        content: '';
        display: block;
        width: 12px;
        height: 12px;
        background-color: #007bff;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .read-more-link:hover i {
        transform: translateX(4px);
    }
</style>


<div style="background-color: #f8f9fa; padding: 50px 0;">
    <div class="container">
        <div class="row">
            
            <div class="col-lg-3 col-md-4">
                <aside style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);">
                    <h2 style="font-family: 'Poppins', sans-serif; font-size: 24px; color: #343a40; margin-top: 0; margin-bottom: 25px; font-weight: 600; border-bottom: 1px solid #e9ecef; padding-bottom: 15px;">
                        Categories
                    </h2>
                    
                    <form action="" method="GET" id="categoryFilterForm">
                        <div>
                            <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                                <div style="display: flex; align-items: center; margin-bottom: 20px;">
                                    <label for="category-<?php echo $category['category_id']; ?>" style="display: flex; align-items: center; width: 100%; cursor: pointer;">
                                        <input type="radio" 
                                               id="category-<?php echo $category['category_id']; ?>" 
                                               name="category"
                                               value="<?php echo $category['category_id']; ?>"
                                               class="custom-radio"
                                               <?php if ($category['category_id'] == $selected_cat_id) echo 'checked'; ?>
                                               style="appearance: none; width: 20px; height: 20px; border: 2px solid #adb5bd; border-radius: 50%; margin-right: 15px; outline: none; position: relative; flex-shrink: 0; transition: border-color 0.2s;"
                                               onchange="this.form.submit()">
                                        
                                        <span style="font-size: 16px; color: #495057; flex-grow: 1; margin-bottom: 0;">
                                            <?php echo htmlentities($category['category_name']); ?>
                                        </span>
                                        
                                        <span style="font-size: 14px; color: #6c757d; font-weight: 500;">
                                            (<?php echo $category['publication_count']; ?>)
                                        </span>
                                    </label>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <?php if (!empty($search_term)): ?>
                            <input type="hidden" name="search" value="<?php echo htmlentities($search_term); ?>">
                        <?php endif; ?>
                    </form>
                </aside>
            </div>

            <div class="col-lg-9 col-md-8">
                <main style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);">
                    
                    <div class="publications-header-flex" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e9ecef;">
                        <div style="font-size: 16px; color: #495057; font-weight: 500;">
                            Showing <?php echo mysqli_num_rows($publications_result); ?> Results
                        </div>
                        
                        <form action="" method="GET" class="search-bar-responsive" style="display: flex; align-items: center; border: 1px solid #dee2e6; border-radius: 25px; overflow: hidden; max-width: 350px; width: 100%; background-color: #ffffff; padding: 5px 8px 5px 15px;">
                            <input type="hidden" name="category" value="<?php echo $selected_cat_id; ?>">
                            <input type="text" name="search" placeholder="Search this category..." value="<?php echo htmlentities($search_term); ?>" style="border: none; outline: none; padding: 8px 10px; flex-grow: 1; font-size: 15px; background: transparent;">
                            <button type="submit" aria-label="Search" style="background: none; border: none; padding: 8px; cursor: pointer; color: #6c757d; font-size: 16px;">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <section>
                        <?php if (mysqli_num_rows($publications_result) > 0): ?>
                            <?php $counter = 1; ?>
                            <?php while ($publication = mysqli_fetch_assoc($publications_result)): ?>
                                <article style="padding-bottom: 25px; margin-bottom: 25px; border-bottom: 1px solid #e9ecef;">
                                    <h3 style="font-size: 22px; color: #343a40; margin: 0 0 12px 0; line-height: 1.4; font-weight: 600;">
                                        <span style="color: #007bff; margin-right: 10px;"><?php echo str_pad($counter, 2, '0', STR_PAD_LEFT); ?>.</span>
                                        <?php echo htmlentities($publication['title']); ?>, <?php echo date('F j, Y', strtotime($publication['publication_date'])); ?>
                                    </h3>
                                    <p style="font-size: 15px; color: #6c757d; margin-bottom: 8px;"><?php echo htmlentities($publication['conference_details']); ?></p>
                                    <!-- ✅ CORRECTION: Replaced ?? operator with isset() for better compatibility -->
                                    <p style="font-size: 15px; color: #007bff; font-weight: 500; margin-bottom: 15px;">By - <?php echo htmlentities(isset($publication['authors']) ? $publication['authors'] : 'N/A'); ?></p>
                                    <a href="#" class="read-more-link" style="display: inline-flex; align-items: center; color: #495057; text-decoration: none; font-weight: 500; font-size: 15px;">
                                        Read more <i class="fa fa-chevron-right" style="margin-left: 8px; font-size: 12px; transition: transform 0.2s;"></i>
                                    </a>
                                </article>
                                <?php $counter++; ?>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px;">
                                <p style="font-size: 18px; color: #6c757d;">No publications found for your criteria.</p>
                                <p style="color: #888;">Try a different category or search term.</p>
                            </div>
                        <?php endif; ?>
                    </section>
                </main>
            </div>
        </div>
    </div>
</div>

    <!--links are not clicble solved by script-->

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Your live search javascript is here...
        });
    </script>
<?php 
// Include your standard footer file
include_once('include/footer.php'); 
?>

