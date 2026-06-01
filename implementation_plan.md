@startuml
left to right direction

skinparam usecase {
  BackgroundColor white
  BorderColor #4CAF50
  ArrowColor #444444
  Shadowing false
}
skinparam actorStyle awesome

'=== ACTORS ===
actor "นักวิจัย/หัวหน้าโครงการ\n(Researcher/RL)" as RL
actor "เจ้าหน้าที่บริหารงานวิจัย\n(Research Admin)" as RA
actor "คณะกรรมการพิจารณา/จริยธรรม\n(Review Committee)" as COM
actor "ผู้บริหาร/คณบดี\n(Executive/Dean)" as EX
actor "เจ้าหน้าที่ประกันคุณภาพ\n(QA/Strategy)" as QA
actor "แหล่งทุนภายนอก\n(External Funder)" as Funder
actor "ผู้ดูแลระบบ\n(System Admin)" as SA

'=== SYSTEM BOUNDARY ===
rectangle "Research & Innovation\nManagement System (RIMS)" as RIMS {

  '--- 1. Proposal & Grant Management (Registration) ---
  package "Proposal & Grant Management" as PROPOSAL {
    usecase "UC1 ลงทะเบียนหัวข้อวิจัยและขอทุน\n(Submit Research Proposal)" as UC1
    usecase "UC2 จัดการงบประมาณและแหล่งทุน\n(Manage Budget & Fund Sources)" as UC2
    usecase "UC3 พิจารณาและอนุมัติข้อเสนอ\n(Review & Approve Proposal)" as UC3
  }

  '--- 2. Progress & Document Tracking ---
  package "Tracking & Progress" as TRACK {
    usecase "UC4 อัปเดตความก้าวหน้าและ Milestone\n(Update Progress & Milestones)" as UC4
    usecase "UC5 จัดเก็บ/ส่งรายงานฉบับสมบูรณ์\n(Submit Final Report & Documents)" as UC5
    usecase "UC6 จัดการไฟล์ข้อมูลวิจัย (Data Archive)\n(Manage Research Data Files)" as UC6
  }

  '--- 3. Output & IP Management ---
  package "Output & Intellectual Property" as OUTPUT {
    usecase "UC7 ลงทะเบียนผลงานตีพิมพ์/บทความ\n(Register Publication Output)" as UC7
    usecase "UC8 ลงทะเบียนสิทธิบัตร/ผลงานสร้างสรรค์\n(Register IP/Creative Works)" as UC8
    usecase "UC9 ตรวจสอบผลงานซ้ำซ้อน\nและนำเข้าฐานข้อมูลภายนอก\n(Check Plagiarism & External Export)" as UC9
  }

  '--- 4. Metrics & Strategic Reporting ---
  package "Metrics & Strategic Reporting" as REPORT {
    usecase "UC10 คำนวณตัวชี้วัดสมรรถนะวิจัย (H-Index, Grants)\n(Calculate Research Performance Metrics)" as UC10
    usecase "UC11 Dashboard ผลงานวิจัยรายบุคคล/รายสาขา\n(View Research Performance Dashboard)" as UC11
    usecase "UC12 ออกรายงานเพื่อตอบ EdPEx/BSC/QA\n(Generate Strategic Reports)" as UC12
  }

  '--- 5. Admin ---
  package "Administration" as ADM {
    usecase "UC13 จัดการข้อมูลหลัก (แหล่งทุน, วารสาร)\n(Manage Master Data & Tiers)" as UC13
  }
}

'=== RELATIONSHIPS ===

' Proposal
RL --> UC1
RL --> UC2
COM --> UC3
RA --> UC3 : «notify result»

' Tracking
RL --> UC4
RL --> UC5
RL --> UC6
Funder --> UC5 : «receives report»
UC4 .> UC1 : «uses proposal info»

' Output & IP
RL --> UC7
RL --> UC8
RA --> UC9
UC7 .> UC9 : «check standard»

' Reporting
EX --> UC11
QA --> UC12
RA --> UC12
UC10 .> UC7 : «uses publication data»
UC11 .> UC10 : «uses metrics»

' Admin
SA --> UC13
UC1 .> UC13 : «uses master data»

@enduml