#!/bin/bash

echo "=========================================="
echo "  PERBAIKAN AUTO TEST"
echo "=========================================="

# 1. Fix password guru
echo ""
echo "1. Memperbaiki password guru..."
mysql -u root -ptkjtkj << EOF
USE 34_Sistem_informasiSIARG;
UPDATE users SET password_hash = '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE username = 'guru1';
EOF
echo "   ✅ Password guru diubah menjadi 'password'"
echo "   💡 Atau jalankan: php -r \"echo password_hash('Guru123!', PASSWORD_DEFAULT);\""

# 2. Hapus role test yang sudah ada
echo ""
echo "2. Membersihkan role test lama..."
mysql -u root -ptkjtkj << EOF
USE 34_Sistem_informasiSIARG;
DELETE FROM role_permissions WHERE role_id IN (SELECT id FROM roles WHERE nama_role LIKE 'test_%');
DELETE FROM roles WHERE nama_role LIKE 'test_%';
EOF
echo "   ✅ Role test dibersihkan"

# 3. Cek role yang tersedia
echo ""
echo "3. Role yang tersedia:"
mysql -u root -ptkjtkj -e "USE 34_Sistem_informasiSIARG; SELECT id, nama_role FROM roles;"

# 4. Test manual create role dengan nama unik
TIMESTAMP=$(date +%s)
echo ""
echo "4. Test create role 'test_$TIMESTAMP'..."
curl -s -X POST http://localhost:8080/api/roles \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d "{\"nama_role\":\"test_$TIMESTAMP\",\"deskripsi\":\"Auto test\"}" | jq .

echo ""
echo "=========================================="
echo "✅ PERBAIKAN SELESAI"
echo "=========================================="
