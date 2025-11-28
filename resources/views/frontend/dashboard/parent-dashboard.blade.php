@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<!-- Breadcrumb -->
<div class="breadcrumb-wrapper" style="background-color: #e0e0e0; padding-top: 50px;  border-bottom: 1px solid #d0d0d0;">
    <div class="container" style=" padding: 20px;">
        <div class="breadcumb-menu-wrap" style=" margin: 9px 0 0 0;">
            <ul class="breadcumb-menu">
                <li><a href="{{ route('frontend.index') }}">Home</a></li>
                <li>Parent Dashboard</li>
            </ul>
        </div>
    </div>
</div>

<section class="space-top space-extra-bottom parent-dashboard-wrapper" style="padding-top: 60px;">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Left Sidebar - Account Menu -->
            <div class="col-lg-3 mb-4">
                @include('frontend.dashboard.partials.account-sidebar')
            </div>

            <!-- Right Content Area -->
            <div class="col-lg-9">
        @if(isset($profiles) && count($profiles) > 0)
            <!-- Tabbed Interface -->
            <div class="tabbed-interface-wrapper">
                <!-- Tab Navigation -->
                <div class="tab-navigation">
                    @foreach($profiles as $profile)
                        <a href="{{ route('frontend.parent.dashboard', ['student_id' => $profile['id']]) }}" 
                           class="tab-button {{ (isset($selectedProfile) && $selectedProfile['id'] == $profile['id']) ? 'active' : '' }}">
                            <span class="student-name">{{ $profile['student_name'] }}</span>
                        </a>
                    @endforeach
                    <a href="#" class="tab-button add-tab" data-bs-toggle="modal" data-bs-target="#addStudentModal" onclick="prepareAddStudentModal()">
                        <i class="fas fa-plus"></i> Add Student
                    </a>
                </div>
                <div class="mobile-add-student d-none">
                    <button type="button" class="vs-btn w-100" data-bs-toggle="modal" data-bs-target="#addStudentModal" onclick="prepareAddStudentModal()" style="background: linear-gradient(135deg, #8c4fcf, #490D59); border: none; border-radius: 12px;">
                        <i class="fas fa-plus me-2"></i> Add Student
                    </button>
                </div>

                @if(isset($selectedProfile))
                    <!-- Content Container -->
                    <div class="tab-content-container {{ (isset($selectedProfile) && $selectedProfile['id'] == $selectedProfile['id']) ? 'active-content' : '' }}">
                        <div class="tab-content-inner">
                            <!-- School and Student Info Card -->
                            <div class="card border-0 mb-4 student-info-card" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); position: relative;">
                                <div class="card-body p-4">
                                    <!-- Edit and Delete Buttons - Top Right -->
                                    <div class="d-flex justify-content-end gap-2 mb-3" style="position: absolute; top: 16px; right: 16px;">
                                        <button type="button"
                                            class="btn btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#addStudentModal"
                                            onclick="prefillStudentModal({{ json_encode($selectedProfile) }})"
                                            style="background-color: #ffffffff; color: #490D59; border: 2px solid #490D59; padding: 8px 18px; border-radius: 30px;"
                                            title="Edit Profile">
                                            <i class="fas fa-edit me-2"></i> Edit Profile
                                        </button>
                                        <form action="{{ route('frontend.parent.delete-profile', ['profileId' => $selectedProfile['id']]) }}" 
                                            method="POST" 
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this student profile? This action cannot be undone.');">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-sm" 
                                                    style="background: linear-gradient(135deg, #ff6b6b, #d90429); color: #ffffff; border: none; padding: 8px 18px; border-radius: 30px;"
                                                    title="Delete Profile">
                                                <i class="fas fa-trash me-2"></i> Delete Profile
                                            </button>
                                        </form>
                                    </div>
                                    <div class="row align-items-center">
                                        <!-- School Logo and Info -->
                                        <div class="col-md-12">
                                            <!-- School Name and Logo - Horizontal Layout -->
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <!-- Logo on Left -->
                                                <div class="school-logo-container" style="flex-shrink: 0;">
                                                    @if(isset($schoolLogo) && $schoolLogo)
                                                        <img src="{{ $schoolLogo }}" alt="{{ $selectedProfile['school_name'] }}" class="school-logo-img">
                                                    @else
                                                        <div class="school-logo-placeholder">
                                                            <i class="fas fa-school"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <!-- School Name and Location on Right -->
                                                <div class="flex-grow-1">
                                                    <p class="mb-1" style="color: #333; font-weight: 500; font-size: 1rem;"><strong>School:</strong> {{ $selectedProfile['school_name'] }}</p>
                                                    @if(isset($schoolAddress) && $schoolAddress)
                                                        <p class="mb-0 text-muted small">{{ $schoolAddress }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <!-- Student Info Below -->
                                            <div>
                                                <div class="bg-light p-3 rounded mt-2" style="border-left: 4px solid #490D59; box-shadow: 0 2px 6px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between; gap: 15px;">
                                                    <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                                                        <span class="small"><strong>Student:</strong> {{ $selectedProfile['student_name'] }}</span>
                                                        <span class="small"><strong>Grade:</strong> {{ $selectedProfile['grade'] }}</span>
                                                        <span class="small"><strong>Gender:</strong> {{ ucfirst($selectedProfile['gender']) }}</span>
                                                    </div>
                                                    <a href="{{ route('frontend.parent.store', ['profile_id' => $selectedProfile['id']]) }}" class="btn btn-sm" style="background-color: #6f42c1; color: #ffffff; border: none; border-radius: 30px; padding: 8px 16px; transition: all 0.3s; flex-shrink: 0;">
                                                        <i class="fas fa-shopping-bag me-1"></i> Shop Now
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Purchased Products Section -->
                            <div class="mb-3">
                                <h5 class="mb-3" style="color: #333; font-weight: 600;">Purchased Products</h5>
                            </div>
                            
                            @if(isset($purchasedProducts) && count($purchasedProducts) > 0)
                                <div class="row g-3">
                                    @foreach(array_slice($purchasedProducts, 0, 4) as $product)
                                        <div class="col-md-6">
                                            <div class="card border" style="border-radius: 8px;">
                                                <div class="card-body p-3">
                                                    <div class="d-flex gap-3">
                                                        <div class="flex-shrink-0">
                                                            @if(isset($product['image']) && $product['image'])
                                                                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px;">
                                                            @else
                                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                                    <i class="fas fa-image text-muted"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1" style="font-size: 0.95rem; font-weight: 600;">{{ $product['name'] }}</h6>
                                                            <p class="text-muted mb-0 small" style="font-size: 0.85rem;">{{ Str::limit($product['description'] ?? '', 50) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="card border-0 text-center py-5" style="background-color: #f8f9fa;">
                                    <div class="card-body">
                                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-3">No purchased products yet.</p>
                                        <div class="mt-4">
                                        <a href="{{ route('frontend.parent.store', ['profile_id' => $selectedProfile['id']]) }}" class="vs-btn w-100" style="background: #490D59; border: none; border-radius: 8px;">
                                            <i class="fas fa-shopping-bag me-2"></i> Shop Now
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @else
            <!-- Empty State -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-user-circle fa-5x text-muted mb-3"></i>
                            <h4 class="mb-3">Welcome to Your Dashboard</h4>
                            <p class="text-muted mb-4">Get started by creating a student profile to begin shopping for uniforms.</p>
                            <button type="button" class="vs-btn" style="background: linear-gradient(135deg, #8c4fcf, #490D59); border: none; border-radius: 30px;" data-bs-toggle="modal" data-bs-target="#addStudentModal" onclick="prepareAddStudentModal()">
                                <i class="fas fa-plus me-2"></i> Add Student
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
            </div>
        </div>
    </div>
</section>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered modal-md" style="z-index: 10000; max-width: 500px;">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.15); position: relative; z-index: 10001;">
            <div class="modal-header" style="border-bottom: 1px solid #e0e0e0; padding: 15px 20px; position: relative;">
                <h5 class="modal-title mb-0" id="addStudentModalLabel" style="font-weight: 600; color: #333; font-size: 1.1rem;">Add Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="opacity: 1; background: #dc3545; border-radius: 50%; width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center; margin: 0; position: absolute; top: 15px; right: 20px;">
                    <span style="color: white; font-size: 16px; font-weight: bold;">×</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <form id="addStudentForm">
                    <input type="hidden" id="modalProfileId" name="profile_id" value="">
                    <div class="mb-3">
                        <label for="modalSchoolName" class="form-label" style="font-weight: 500; color: #333; margin-bottom: 8px;">School Name</label>
                        <div class="autocomplete-wrapper-modal" style="position: relative;">
                            <input type="text" id="modalSchoolName" name="school_name" class="form-control" placeholder="Start typing school name" autocomplete="off" required style="border: 1px solid #ddd; border-radius: 6px; padding: 10px 12px;">
                            <div class="suggestion-list-modal" id="modalSchoolSuggestions" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 6px; max-height: 200px; overflow-y: auto; z-index: 10002 !important; display: none; margin-top: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="modalStudentName" class="form-label" style="font-weight: 500; color: #333; margin-bottom: 8px;">Student Name</label>
                        <input type="text" id="modalStudentName" name="student_name" class="form-control" placeholder="Enter student name" required style="border: 1px solid #ddd; border-radius: 6px; padding: 10px 12px;">
                    </div>
                    <div class="mb-3">
                        <label for="modalGrade" class="form-label" style="font-weight: 500; color: #333; margin-bottom: 8px;">Grade</label>
                        <select id="modalGrade" name="grade" class="form-select" required style="border: 1px solid #ddd; border-radius: 6px; padding: 10px 12px;">
                            <option value="">Select Grade</option>
                            <option value="LKG">LKG</option>
                            <option value="UKG">UKG</option>
                            <option value="1">Grade 1</option>
                            <option value="2">Grade 2</option>
                            <option value="3">Grade 3</option>
                            <option value="4">Grade 4</option>
                            <option value="5">Grade 5</option>
                            <option value="6">Grade 6</option>
                            <option value="7">Grade 7</option>
                            <option value="8">Grade 8</option>
                            <option value="9">Grade 9</option>
                            <option value="10">Grade 10</option>
                            <option value="11">Grade 11</option>
                            <option value="12">Grade 12</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modalSection" class="form-label" style="font-weight: 500; color: #333; margin-bottom: 8px;">Section</label>
                        <input type="text" id="modalSection" name="section" class="form-control" placeholder="Enter section (e.g., A, B, C)" required style="border: 1px solid #ddd; border-radius: 6px; padding: 10px 12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500; color: #333; margin-bottom: 8px;">Gender</label>
                        <div class="gender-buttons-modal" style="display: flex; gap: 8px;">
                            <button type="button" class="btn btn-sm gender-btn-modal" data-value="male" style="border: 1px solid #ddd; background: white; color: #333; padding: 6px 20px; border-radius: 6px; flex: 1;">Male</button>
                            <button type="button" class="btn btn-sm gender-btn-modal" data-value="female" style="border: 1px solid #ddd; background: white; color: #333; padding: 6px 20px; border-radius: 6px; flex: 1;">Female</button>
                        </div>
                        <input type="hidden" id="modalGenderValue" name="gender" required>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 12px 20px; margin-top: 15px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border: 1px solid #ddd; background: white; color: #333; padding: 8px 16px; border-radius: 6px; font-size: 0.9rem;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #490D59; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 0.9rem;">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gradeButtons = document.querySelectorAll('.grade-btn-modal');
    const genderButtons = document.querySelectorAll('.gender-btn-modal');
    const modalSchoolInput = document.getElementById('modalSchoolName');
    const modalSuggestionBox = document.getElementById('modalSchoolSuggestions');
    const addStudentForm = document.getElementById('addStudentForm');
    const profileIdField = document.getElementById('modalProfileId');
    const modalTitle = document.getElementById('addStudentModalLabel');
    const modalSubmitBtn = document.querySelector('#addStudentForm button[type="submit"]');
    const addStudentModal = document.getElementById('addStudentModal');

    const schools = [
        {
            name: 'Stanes ICSE School',
            location: 'Peelamedu',
            logo: '{{ asset("assets/img/school_logo/Stanes ICSE logo.png") }}'
        },
        {
            name: 'Stanes School CBSE',
            location: 'Avinashi Road',
            logo: '{{ asset("assets/img/school_logo/Stanes School CBSE logo.jpg") }}'
        },
        {
            name: 'Stanes Anglo Indian Higher Secondary School (AIHSS) – Samacheer',
            location: 'Avinashi Road',
            logo: '{{ asset("assets/img/school_logo/Stanes Anglo Indian Higher Secondary School (AIHSS) – Samacheer logo.png") }}'
        },
        {
            name: 'Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – RS Puram',
            location: 'R S Puram',
            logo: '{{ asset("assets/img/school_logo/Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – RS Puram logo.jpg") }}'
        },
        {
            name: 'Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – Ajjanur',
            location: 'Ajjanur',
            logo: '{{ asset("assets/img/school_logo/Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – Ajjanur logo.jpg") }}'
        },
        {
            name: 'Shri Nehru Vidyalaya Matriculation Higher Secondary School (SNV)',
            location: 'R.S. Puram',
            logo: null
        }
    ];

    const renderModalSuggestions = (value) => {
        const query = value.trim().toLowerCase();
        if (!query) {
            modalSuggestionBox.style.display = 'none';
            modalSuggestionBox.innerHTML = '';
            return;
        }

        const matches = schools.filter(school => 
            school.name.toLowerCase().includes(query) || 
            school.location.toLowerCase().includes(query)
        );
        
        if (matches.length === 0) {
            modalSuggestionBox.style.display = 'none';
            modalSuggestionBox.innerHTML = '';
            return;
        }

        modalSuggestionBox.innerHTML = matches.map(school => 
            `<div class="suggestion-item-modal" data-value="${school.name}" style="padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; gap: 10px;">
                <div style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                    ${school.logo ? `<img src="${school.logo}" alt="${school.name}" style="width: 30px; height: 30px; object-fit: contain; border-radius: 4px;">` : '<div style="width: 30px; height: 30px; background: #f0f0f0; border-radius: 4px;"></div>'}
                </div>
                <div>
                    <div style="font-weight: 500; color: #333;">${school.name}</div>
                    <div style="font-size: 12px; color: #666;">${school.location}</div>
                </div>
            </div>`
        ).join('');
        modalSuggestionBox.style.display = 'block';
    };

    if (modalSchoolInput) {
        modalSchoolInput.addEventListener('input', (e) => {
            renderModalSuggestions(e.target.value);
        });
    }

    if (modalSuggestionBox) {
        modalSuggestionBox.addEventListener('click', (e) => {
            const item = e.target.closest('.suggestion-item-modal');
            if (item) {
                modalSchoolInput.value = item.dataset.value;
                modalSuggestionBox.style.display = 'none';
                modalSuggestionBox.innerHTML = '';
            }
        });
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.autocomplete-wrapper-modal')) {
            if (modalSuggestionBox) {
                modalSuggestionBox.style.display = 'none';
            }
        }
    });

    function setSelection(buttons, value) {
        buttons.forEach(btn => {
            if (value && btn.dataset.value === value) {
                btn.classList.add('active');
                btn.style.backgroundColor = '#490D59';
                btn.style.color = 'white';
                btn.style.borderColor = '#490D59';
            } else {
                btn.classList.remove('active');
                btn.style.backgroundColor = 'white';
                btn.style.color = '#333';
                btn.style.borderColor = '#ddd';
            }
        });
    }

    gradeButtons.forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('modalGradeValue').value = this.dataset.value;
            setSelection(gradeButtons, this.dataset.value);
        });
    });

    genderButtons.forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('modalGenderValue').value = this.dataset.value;
            setSelection(genderButtons, this.dataset.value);
        });
    });

    if (modalSchoolInput) {
        modalSchoolInput.addEventListener('input', (e) => {
            renderModalSuggestions(e.target.value);
        });
    }

    if (modalSuggestionBox) {
        modalSuggestionBox.addEventListener('click', (e) => {
            const item = e.target.closest('.suggestion-item-modal');
            if (item) {
                modalSchoolInput.value = item.dataset.value;
                modalSuggestionBox.style.display = 'none';
                modalSuggestionBox.innerHTML = '';
            }
        });
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.autocomplete-wrapper-modal') && modalSuggestionBox) {
            modalSuggestionBox.style.display = 'none';
        }
    });

    function resetStudentModal() {
        if (!addStudentForm) return;
        addStudentForm.reset();
        document.getElementById('modalGrade').value = '';
        document.getElementById('modalGenderValue').value = '';
        if (profileIdField) profileIdField.value = '';
        setSelection(gradeButtons, null);
        setSelection(genderButtons, null);
        if (modalSuggestionBox) {
            modalSuggestionBox.style.display = 'none';
            modalSuggestionBox.innerHTML = '';
        }
        if (modalTitle) modalTitle.textContent = 'Add Student';
        if (modalSubmitBtn) modalSubmitBtn.textContent = 'Submit';
    }

    window.prepareAddStudentModal = function() {
        resetStudentModal();
    };

    if (addStudentForm) {
        addStudentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const grade = document.getElementById('modalGrade').value;
            const gender = document.getElementById('modalGenderValue').value;
            const schoolName = document.getElementById('modalSchoolName').value;
            const studentName = document.getElementById('modalStudentName').value;
            const section = document.getElementById('modalSection').value;

            if (!grade || !gender || !schoolName || !studentName || !section) {
                alert('Please fill in all required fields');
                return;
            }

            const formData = new FormData();
            formData.append('student_name', studentName);
            formData.append('school_name', schoolName);
            formData.append('grade', grade);
            formData.append('section', section);
            formData.append('gender', gender);
            formData.append('_token', '{{ csrf_token() }}');
            if (profileIdField && profileIdField.value) {
                formData.append('profile_id', profileIdField.value);
            }

            let url = '{{ route("frontend.parent.store-profile") }}';
            if (profileIdField && profileIdField.value) {
                url = '{{ url("/parent/update-profile") }}/' + profileIdField.value;
            }

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    throw new Error('Response is not JSON');
                }
            })
            .then(data => {
                if (data.success) {
                    const modalInstance = bootstrap.Modal.getInstance(addStudentModal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                    
                    const redirectId = data.profile_id ?? (profileIdField ? profileIdField.value : null);
                    const redirectUrl = redirectId 
                        ? '{{ route("frontend.parent.dashboard") }}?student_id=' + redirectId 
                        : '{{ route("frontend.parent.dashboard") }}';
                    window.location.href = redirectUrl;
                } else {
                    let errorMsg = data.message || 'Unable to save student profile.';
                    if (data.errors) {
                        const errorList = Object.values(data.errors).flat().join('\n');
                        errorMsg = errorMsg + '\n\n' + errorList;
                    }
                    alert(errorMsg);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again. ' + error.message);
            });
        });
    }

    window.prefillStudentModal = function(profile) {
        if (!profile || !addStudentForm) return;

        if (modalTitle) modalTitle.textContent = 'Edit Student';
        if (modalSubmitBtn) modalSubmitBtn.textContent = 'Update Student';
        if (profileIdField) profileIdField.value = profile.id || '';

        document.getElementById('modalSchoolName').value = profile.school_name || '';
        document.getElementById('modalStudentName').value = profile.student_name || '';
        document.getElementById('modalSection').value = profile.section || '';

        document.getElementById('modalGrade').value = profile.grade || '';
        document.getElementById('modalGenderValue').value = profile.gender || '';
        setSelection(gradeButtons, profile.grade || null);
        setSelection(genderButtons, profile.gender || null);

        const modalInstance = bootstrap.Modal.getOrCreateInstance(addStudentModal);
        modalInstance.show();
    };

    if (addStudentModal) {
        addStudentModal.addEventListener('show.bs.modal', function () {
            document.body.classList.add('modal-open');
            if (!profileIdField || profileIdField.value === '') {
                resetStudentModal();
            }
        });

        addStudentModal.addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('modal-open');
            resetStudentModal();
        });
    }
});
</script>

<style>
    .tabbed-interface-wrapper {
        max-width: 1200px;
        margin: 0 auto;
    }

    .parent-dashboard-wrapper {
        background-color: #ffffff;
    }

    .tab-navigation {
        display: flex;
        gap: 8px;
        margin-bottom: 0;
        padding: 0;
        align-items: flex-end;
    }

    .tab-button {
        padding: 12px 24px;
        font-weight: 500;
        text-decoration: none;
        border: 2px solid #e0d5f0;
        border-bottom: none;
        border-radius: 0;
        position: relative;
        background-color: #e0d5f0;
        color: #333;
        display: inline-flex;
        align-items: center;
        border-radius: 8px 8px 0 0; /* Rounded top corners for all tabs */
    }

    .tab-button:hover {
        background-color: #d0c5e0;
        color: #333;
    }

    .tab-button.active {
        background-color: #ffffff;
        border-color: #490D59;
        color: #333;
        z-index: 10;
        margin-bottom: -2px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        position: relative;
    }

    .tab-button.active::after {
        content: "";
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: -14px;
        width: 0;
        height: 0;
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-top: 12px solid #490D59;
        transition: none;
    }

    .tab-button .student-name {
        display: inline-block;
    }

    .tab-button.add-tab {
        padding: 12px 20px;
        font-size: 14px;
        background: linear-gradient(135deg, #8c4fcf, #490D59);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        gap: 6px;
        
    }

    .tab-button.add-tab i {
        margin-right: 4px;
    }

    .tab-button.add-tab:hover {
        background-color: #5a1d69;
        border-color: #5a1d69;
        color: #ffffff;
    }

    .tab-content-container {
        background-color: #ffffff;
        border: 2px solid #e0d5f0;
        border-radius: 16px;
        padding: 24px;
        min-height: 400px;
        margin-top: -2px;
        position: relative;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .tab-content-container.active-content {
        border: 2px solid #ffffff !important;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(31, 2, 39, 0.4);
        margin-top: 20px;
    }

    /* Ensure border connects when tab is active */
    .tab-button.active {
        border-bottom: 2px solid #490D59;
    }

    .student-info-card {
        border: 2px solid #e0d5f0 !important;
        border-radius: 16px;
    }

    .tab-content-inner {
        animation: none;
    }

    .school-logo-container {
        width: 64px;
        height: 64px;
        min-width: 64px;
        min-height: 64px;
        border-radius: 50%;
        border: 3px solid #490D59 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ffffff;
        box-shadow: 0 2px 8px rgba(73, 13, 89, 0.2);
        flex-shrink: 0;
        padding: 6px;
        box-sizing: border-box;
        overflow: visible;
        position: relative;
    }

    .school-logo-img {
        width: calc(100% - 12px);
        height: calc(100% - 12px);
        object-fit: contain;
        border-radius: 50%;
        display: block;
    }

    .school-logo-placeholder {
        width: calc(100% - 12px);
        height: calc(100% - 12px);
        border-radius: 50%;
        background-color: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 24px;
    }

    @media (max-width: 991px) {
        .tabbed-interface-wrapper {
            padding: 0;
        }

        .tab-navigation {
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 8px;
            scrollbar-width: thin;
            gap: 6px;
        }

        .tab-navigation::-webkit-scrollbar {
            height: 6px;
        }

        .tab-navigation::-webkit-scrollbar-thumb {
            background: #d0c5e0;
            border-radius: 10px;
        }

        .tab-button {
            flex: 0 0 auto;
            padding: 8px 16px; /* Smaller padding */
            font-size: 14px; /* Smaller font */
            width: auto !important; /* Fit content */
        }
        
        .tab-button.add-tab {
             display: inline-flex !important; /* Ensure visible */
             width: auto !important;
        }

        .tab-content-container {
            padding: 20px;
        }

        .student-info-card .row {
            row-gap: 20px;
        }

        .tab-content-inner .d-flex.justify-content-end.gap-2 {
            justify-content: flex-end !important;
        }
    }

    @media (max-width: 768px) {
        .tab-content-container {
            padding: 18px;
        }

        .tab-button.add-tab {
            border-radius: 8px;
        }

        .school-logo-container {
            margin: 0; /* Align left with text */
        }

        /* Fix Edit/Delete Buttons on Mobile */
        .student-info-card .d-flex.justify-content-end {
            position: relative !important;
            top: 0 !important;
            right: 0 !important;
            margin-bottom: 0px !important; /* Removed margin as requested */
            justify-content: flex-end !important; /* Align right */
            gap: 10px !important;
            width: 100%; /* Ensure container takes full width to allow right alignment */
        }

        .student-info-card .d-flex.justify-content-end button,
        .student-info-card .d-flex.justify-content-end form button {
            flex: 0 0 auto; /* Prevent growing */
            width: auto;
            min-width: 0;
            padding: 4px 10px !important; /* Even smaller padding */
            font-size: 11px !important; /* Smaller font */
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: auto;
        }
        
        /* Stack Student Details */
        .tab-content-inner .bg-light {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 15px;
        }
        
        .tab-content-inner .bg-light > div {
            width: 100%;
            flex-direction: column;
            align-items: flex-start !important;
            gap: 8px !important;
        }

        .tab-content-inner .btn.btn-sm {
            width: 100%;
            margin-bottom: 0px;
        }
    }

    @media (max-width: 575px) {
        .tab-content-container {
            padding: 16px;
        }

        .tab-button {
            font-size: 0.85rem;
        }

        .tab-button.add-tab {
            width: 100%;
        }

        .tab-navigation {
            flex-wrap: wrap;
        }

        .tab-button,
        .tab-navigation a {
            width: 100%;
        }

        .tab-content-inner .btn.btn-sm {
            width: 100%;
        }

        .row.g-3 .col-md-6 {
            width: 100%;
        }
    }

    /* Ensure modal appears on top */
    #addStudentModal {
        z-index: 9999 !important;
    }

    #addStudentModal .modal-backdrop {
        z-index: 9998 !important;
    }

    #addStudentModal .modal-dialog {
        z-index: 10000 !important;
    }

    #addStudentModal .modal-content {
        z-index: 10001 !important;
        position: relative;
    }

    /* Ensure autocomplete dropdown appears above modal content */
    .suggestion-list-modal {
        z-index: 10002 !important;
        position: absolute !important;
    }

    .autocomplete-wrapper-modal {
        position: relative;
        z-index: 10003;
    }

    /* Blur effect for tabs when modal is open */
    body.modal-open .tab-navigation,
    body.modal-open .tab-content-container {
        filter: blur(3px);
        opacity: 0.5;
        pointer-events: none;
        transition: all 0.3s ease;
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 10px;
            max-width: calc(100% - 20px);
        }

        .modal-content {
            border-radius: 8px;
        }

        .modal-header {
            padding: 15px 20px !important;
        }

        .modal-title {
            font-size: 18px;
        }

        .modal-body {
            padding: 20px !important;
        }

        .modal-header .btn-close {
            top: 15px !important;
            right: 20px !important;
            width: 28px !important;
            height: 28px !important;
        }

        .grade-buttons-modal {
            gap: 6px !important;
        }

        .grade-buttons-modal .btn {
            padding: 5px 10px !important;
            font-size: 12px !important;
        }

        .gender-buttons-modal {
            flex-direction: column;
            gap: 8px;
        }

        .gender-buttons-modal .btn {
            width: 100%;
        }

        .form-label {
            font-size: 14px;
        }

        .form-control {
            font-size: 14px;
            padding: 8px 10px !important;
        }

        .modal-footer {
            flex-direction: column;
            gap: 10px;
        }

        .modal-footer .btn {
            width: 100%;
        }

        .suggestion-list-modal {
            max-height: 150px !important;
            font-size: 13px;
        }

        .suggestion-item-modal {
            padding: 8px 10px !important;
        }

        /* Dashboard responsive */
        .col-lg-3 {
            margin-bottom: 20px;
        }
    @media (max-width: 991px) {
        .tab-navigation {
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 14px; /* Updated padding */
            scrollbar-width: none;
        }
        
        .tab-content-container.active-content {
            margin-top: 0px !important; /* Removed top margin */
        }
    }

    @media (max-width: 768px) {
        .col-lg-9 {
            padding-left: 12px;
        }

        .parent-dashboard-wrapper {
            padding-top: 10px !important; /* Reduced padding as requested */
        }

        .tab-navigation {
            overflow-x: auto;
            flex-wrap: nowrap;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 14px; /* Ensure consistency */
            scrollbar-width: none; /* Firefox */
        }
        .tab-navigation::-webkit-scrollbar {
            display: none; /* Chrome/Safari */
        }

        .tab-button {
            min-width: auto; /* Removed fixed width to fit content */
            padding: 10px 16px !important;
            font-size: 14px;
        }

        .tab-content-container {
            padding: 15px !important;
        }

        .student-info-card .row {
            flex-direction: column;
        }

        .student-info-card .col-md-6 {
            margin-bottom: 15px;
        }

        .school-logo-container {
            width: 80px !important;
            height: 80px !important;
        }
    }

    @media (max-width: 576px) {
        .modal-dialog {
            margin: 5px;
            max-width: calc(100% - 10px);
        }

        .modal-header {
            padding: 12px 15px !important;
        }

        .modal-body {
            padding: 15px !important;
        }

        .grade-buttons-modal .btn {
            padding: 4px 8px !important;
            font-size: 11px !important;
        }

        .tab-button {
            padding: 8px 12px !important;
            font-size: 12px;
        }

        .tab-button.add-tab {
            padding: 8px 12px !important;
            font-size: 12px;
        }
    }
</style>
@endsection
