<?php
include "../../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../unRegistedUserModule/loginPage.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$updateAccountNotif = "";
// Fetch user info for form
$stmt = $conn->prepare("SELECT FIRST_NAME, LAST_NAME, PHONE, ADDRESS FROM USER_DETAILS WHERE USER_ID = :user_id");
$stmt->execute([':user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['customerChangeInfo'])) {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $phone      = trim($_POST['phone']);
    $address    = trim($_POST['address']);
    $password   = trim($_POST['password']);

    // Update USER_DETAILS
    $sql = "UPDATE USER_DETAILS 
            SET FIRST_NAME = :first_name, LAST_NAME = :last_name, 
                PHONE = :phone, ADDRESS = :address 
            WHERE USER_ID = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':first_name' => $first_name,
        ':last_name'  => $last_name,
        ':phone'      => $phone,
        ':address'    => $address,
        ':user_id'    => $user_id
    ]);

    // Update password if provided
    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_ARGON2ID);
        $sql = "UPDATE USER_ACCOUNT SET PASSWORD = :password WHERE USER_ID = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':password' => $hashed,
            ':user_id'  => $user_id
        ]);
    }

    // Refresh user data after update
    $updateAccountNotif = "<script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Account details updated!',
                                    });
                          </script>";
}
?>


<h2 class="text-2xl font-bold mb-2">Account</h2>
<p>Update your personal info and password.</p>

<form method="POST" class="mt-4 space-y-4 bg-white rounded shadow p-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">First Name</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($user['FIRST_NAME']) ?>"
            class="w-full mt-1 border rounded p-2" required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Last Name</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($user['LAST_NAME']) ?>"
            class="w-full mt-1 border rounded p-2" required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['PHONE']) ?>"
            class="w-full mt-1 border rounded p-2" required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Address</label>
        <textarea name="address" class="w-full mt-1 border rounded p-2" required><?= htmlspecialchars($user['ADDRESS']) ?></textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">New Password</label>
        <input type="password" name="password" placeholder="Leave blank if unchanged"
            class="w-full mt-1 border rounded p-2">
    </div>

    <button type="submit" name="customerChangeInfo" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
        Save Changes
    </button>
</form>
<?php
echo $updateAccountNotif;
?>