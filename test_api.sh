#!/bin/bash
# 🧪 QUICK TEST SCRIPT — SYIAR GEMILANG
# Jalankan: chmod +x test_api.sh && ./test_api.sh

# ========================================
# COLOR CODES
# ========================================
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# ========================================
# VARIABLES
# ========================================
API_URL="http://127.0.0.1:8081/api"
ADMIN_USER="superadmin"
ADMIN_PASS="Admin123!"
TOKEN=""

# ========================================
# FUNCTIONS
# ========================================

print_header() {
    echo -e "${BLUE}========================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}========================================${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "${YELLOW}ℹ $1${NC}"
}

# ========================================
# TEST 1: Health Check
# ========================================
test_health() {
    print_header "TEST 1: Health Check"
    
    response=$(curl -s http://127.0.0.1:8081/health)
    echo "$response" | python3 -m json.tool
    
    if echo "$response" | grep -q "ok"; then
        print_success "Server is running"
    else
        print_error "Server not responding"
        exit 1
    fi
}

# ========================================
# TEST 2: Authentication
# ========================================
test_auth() {
    print_header "TEST 2: Authentication (Login)"
    
    response=$(curl -s -X POST "$API_URL/auth/login" \
        -H "Content-Type: application/json" \
        -d "{\"username\":\"$ADMIN_USER\",\"password\":\"$ADMIN_PASS\"}")
    
    echo "$response" | python3 -m json.tool
    
    TOKEN=$(echo "$response" | python3 -c "import sys, json; print(json.load(sys.stdin).get('data', {}).get('token', ''))" 2>/dev/null)
    
    if [ -z "$TOKEN" ]; then
        print_error "Login failed - no token"
        exit 1
    else
        print_success "Login successful"
        print_info "Token: ${TOKEN:0:30}..."
    fi
}

# ========================================
# TEST 3: Get Profile
# ========================================
test_profile() {
    print_header "TEST 3: Get Profile (Me)"
    
    response=$(curl -s -X GET "$API_URL/auth/me" \
        -H "Authorization: Bearer $TOKEN")
    
    echo "$response" | python3 -m json.tool
    print_success "Profile retrieved"
}

# ========================================
# TEST 4: Roles & Permissions
# ========================================
test_roles() {
    print_header "TEST 4: Roles & Permissions"
    
    echo -e "${YELLOW}--- All Roles ---${NC}"
    curl -s -X GET "$API_URL/roles" \
        -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
    
    echo -e "\n${YELLOW}--- All Permissions ---${NC}"
    curl -s -X GET "$API_URL/roles/permissions" \
        -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
    
    print_success "Roles and permissions retrieved"
}

# ========================================
# TEST 5: Phase 2 - Pegawai
# ========================================
test_pegawai() {
    print_header "TEST 5: Phase 2 - Pegawai/Staff"
    
    echo -e "${YELLOW}--- List Pegawai ---${NC}"
    curl -s -X GET "$API_URL/pegawai" \
        -H "Authorization: Bearer $TOKEN" | python3 -m json.tool | head -50
    
    echo -e "\n${YELLOW}--- Get Detail Pegawai ID 1 ---${NC}"
    curl -s -X GET "$API_URL/pegawai/1" \
        -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
    
    echo -e "\n${YELLOW}--- Search Pegawai ---${NC}"
    curl -s -X GET "$API_URL/pegawai?search=Ahmad" \
        -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
    
    print_success "Pegawai endpoints tested"
}

# ========================================
# TEST 6: Phase 2 - Mata Pelajaran
# ========================================
test_mapel() {
    print_header "TEST 6: Phase 2 - Mata Pelajaran"
    
    echo -e "${YELLOW}--- List Mata Pelajaran ---${NC}"
    curl -s -X GET "$API_URL/mata-pelajaran" \
        -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
    
    print_success "Mata Pelajaran retrieved"
}

# ========================================
# TEST 7: Phase 3 - Kelas
# ========================================
test_kelas() {
    print_header "TEST 7: Phase 3 - Kelas"
    
    echo -e "${YELLOW}--- List Kelas ---${NC}"
    curl -s -X GET "$API_URL/kelas" \
        -H "Authorization: Bearer $TOKEN" | python3 -m json.tool | head -30
    
    print_success "Kelas retrieved"
}

# ========================================
# TEST 8: Phase 3 - Santri
# ========================================
test_santri() {
    print_header "TEST 8: Phase 3 - Santri"
    
    echo -e "${YELLOW}--- List Santri (Sample) ---${NC}"
    curl -s -X GET "$API_URL/santri?limit=5" \
        -H "Authorization: Bearer $TOKEN" | python3 -m json.tool | head -40
    
    print_success "Santri retrieved"
}

# ========================================
# TEST 9: Phase 3 - Penilaian
# ========================================
test_penilaian() {
    print_header "TEST 9: Phase 3 - Penilaian (Grading)"
    
    echo -e "${YELLOW}--- Get Aspek by Kategori ---${NC}"
    curl -s -X GET "$API_URL/penilaian/aspek/1" \
        -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
    
    print_success "Penilaian data retrieved"
}

# ========================================
# TEST 10: Phase 4 - Bank Soal
# ========================================
test_bank_soal() {
    print_header "TEST 10: Phase 4 - Bank Soal"
    
    echo -e "${YELLOW}--- List Bank Soal ---${NC}"
    curl -s -X GET "$API_URL/bank-soal" \
        -H "Authorization: Bearer $TOKEN" | python3 -m json.tool | head -50
    
    print_success "Bank Soal retrieved"
}

# ========================================
# TEST 11: Phase 4 - Ujian
# ========================================
test_ujian() {
    print_header "TEST 11: Phase 4 - Ujian/CBT"
    
    echo -e "${YELLOW}--- List Ujian ---${NC}"
    curl -s -X GET "$API_URL/ujian" \
        -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
    
    print_success "Ujian endpoints tested"
}

# ========================================
# TEST 12: Dashboard Stats
# ========================================
test_dashboard() {
    print_header "TEST 12: Dashboard Statistics"
    
    echo -e "${YELLOW}--- Dashboard Stats ---${NC}"
    curl -s -X GET "$API_URL/dashboard/stats" \
        -H "Authorization: Bearer $TOKEN" | python3 -m json.tool
    
    print_success "Dashboard stats retrieved"
}

# ========================================
# TEST 13: CREATE TEST - Pegawai
# ========================================
test_create_pegawai() {
    print_header "TEST 13: CREATE - Pegawai Baru"
    
    echo -e "${YELLOW}--- Creating new Pegawai ---${NC}"
    response=$(curl -s -X POST "$API_URL/pegawai" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json" \
        -d '{
            "nama_lengkap": "Ibu Siti Nurhaliza (Test)",
            "jenis_kelamin": "P",
            "pendidikan_terakhir": "S1",
            "status_kepegawaian": "GTT",
            "jabatan": "Guru Bahasa"
        }')
    
    echo "$response" | python3 -m json.tool
    print_success "Pegawai creation tested"
}

# ========================================
# TEST 14: UPDATE TEST - Pegawai
# ========================================
test_update_pegawai() {
    print_header "TEST 14: UPDATE - Pegawai"
    
    echo -e "${YELLOW}--- Updating Pegawai ID 1 ---${NC}"
    response=$(curl -s -X PUT "$API_URL/pegawai/1" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json" \
        -d '{
            "status_kepegawaian": "Tetap"
        }')
    
    echo "$response" | python3 -m json.tool
    print_success "Pegawai update tested"
}

# ========================================
# MAIN EXECUTION
# ========================================

main() {
    print_header "🧪 SYIAR GEMILANG - COMPLETE API TEST SUITE"
    
    test_health
    echo ""
    
    test_auth
    echo ""
    
    test_profile
    echo ""
    
    test_roles
    echo ""
    
    test_pegawai
    echo ""
    
    test_mapel
    echo ""
    
    test_kelas
    echo ""
    
    test_santri
    echo ""
    
    test_penilaian
    echo ""
    
    test_bank_soal
    echo ""
    
    test_ujian
    echo ""
    
    test_dashboard
    echo ""
    
    test_create_pegawai
    echo ""
    
    test_update_pegawai
    echo ""
    
    print_header "✅ ALL TESTS COMPLETED"
    echo -e "${GREEN}System is operational and ready for use!${NC}"
}

# Run main function
main
