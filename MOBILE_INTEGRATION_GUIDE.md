# Fundlink Mobile Integration Guide (Flutter)

**Stack**: Flutter + Dart  
**Auth**: Laravel Sanctum Bearer Token  
**Base URL**: `https://bahamud.my.id/api`

---

## Setup

### pubspec.yaml dependencies
```yaml
dependencies:
  flutter:
    sdk: flutter
  http: ^1.2.0
  shared_preferences: ^2.2.0
  flutter_secure_storage: ^9.0.0
  image_picker: ^1.0.7
  intl: ^0.19.0
```

---

## 1. Konfigurasi & Konstanta

```dart
// lib/core/config.dart
class AppConfig {
  static const String _prodUrl = 'https://bahamud.my.id/api';
  static const String _devUrl  = 'http://10.0.2.2:8000/api'; // Android emulator
  // static const String _devUrl = 'http://127.0.0.1:8000/api'; // iOS simulator

  static const bool isProduction = bool.fromEnvironment('dart.vm.product');

  static String get baseUrl => isProduction ? _prodUrl : _devUrl;
}
```

---

## 2. Model Classes

```dart
// lib/models/user_model.dart
class UserModel {
  final int id;
  final String name;
  final String email;
  final String role;
  final int? unitId;
  final UnitModel? unit;
  final String? profilePhotoUrl;

  const UserModel({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    this.unitId,
    this.unit,
    this.profilePhotoUrl,
  });

  bool get isAdmin => role == 'admin';

  factory UserModel.fromJson(Map<String, dynamic> json) => UserModel(
    id:              json['id'],
    name:            json['name'],
    email:           json['email'],
    role:            json['role'],
    unitId:          json['unit_id'],
    unit:            json['unit'] != null ? UnitModel.fromJson(json['unit']) : null,
    profilePhotoUrl: json['profile_photo_url'],
  );
}

// lib/models/unit_model.dart
class UnitModel {
  final int id;
  final String name;
  final String? address;
  final String? googleMapsUrl;
  final int? usersCount;
  final double? saldo;

  const UnitModel({
    required this.id,
    required this.name,
    this.address,
    this.googleMapsUrl,
    this.usersCount,
    this.saldo,
  });

  factory UnitModel.fromJson(Map<String, dynamic> json) => UnitModel(
    id:            json['id'],
    name:          json['name'],
    address:       json['address'],
    googleMapsUrl: json['google_maps_url'],
    usersCount:    json['users_count'],
    saldo:         json['saldo'] != null ? (json['saldo'] as num).toDouble() : null,
  );
}

// lib/models/transaction_model.dart
class TransactionModel {
  final int id;
  final String type;
  final double amount;
  final String category;
  final String? description;
  final String transactionDate;
  final String? attachmentUrl;
  final UnitModel? unit;
  final Map<String, dynamic>? recordedBy;
  final String? createdAt;

  const TransactionModel({
    required this.id,
    required this.type,
    required this.amount,
    required this.category,
    this.description,
    required this.transactionDate,
    this.attachmentUrl,
    this.unit,
    this.recordedBy,
    this.createdAt,
  });

  bool get isPemasukan => type == 'pemasukan';

  factory TransactionModel.fromJson(Map<String, dynamic> json) => TransactionModel(
    id:              json['id'],
    type:            json['type'],
    amount:          (json['amount'] as num).toDouble(),
    category:        json['category'],
    description:     json['description'],
    transactionDate: json['transaction_date'],
    attachmentUrl:   json['attachment_url'],
    unit:            json['unit'] != null ? UnitModel.fromJson(json['unit']) : null,
    recordedBy:      json['recorded_by'],
    createdAt:       json['created_at'],
  );
}

// lib/models/notification_model.dart
class NotificationModel {
  final int id;
  final String title;
  final String message;
  final String type;
  final bool isRead;
  final String createdAt;

  const NotificationModel({
    required this.id,
    required this.title,
    required this.message,
    required this.type,
    required this.isRead,
    required this.createdAt,
  });

  factory NotificationModel.fromJson(Map<String, dynamic> json) => NotificationModel(
    id:        json['id'],
    title:     json['title'],
    message:   json['message'],
    type:      json['type'],
    isRead:    json['is_read'],
    createdAt: json['created_at'],
  );
}

// lib/models/pagination_model.dart
class PaginationMeta {
  final int currentPage;
  final int lastPage;
  final int perPage;
  final int total;
  final bool hasMore;

  const PaginationMeta({
    required this.currentPage,
    required this.lastPage,
    required this.perPage,
    required this.total,
    required this.hasMore,
  });

  factory PaginationMeta.fromJson(Map<String, dynamic> json) => PaginationMeta(
    currentPage: json['current_page'],
    lastPage:    json['last_page'],
    perPage:     json['per_page'],
    total:       json['total'],
    hasMore:     json['has_more'],
  );
}
```

---

## 3. Exception Classes

```dart
// lib/core/exceptions.dart
class ApiException implements Exception {
  final String message;
  final int? statusCode;
  const ApiException(this.message, {this.statusCode});

  @override
  String toString() => message;
}

class UnauthorizedException extends ApiException {
  const UnauthorizedException() : super('Sesi berakhir. Silakan login kembali.', statusCode: 401);
}

class ForbiddenException extends ApiException {
  const ForbiddenException() : super('Anda tidak memiliki izin untuk aksi ini.', statusCode: 403);
}

class NotFoundException extends ApiException {
  const NotFoundException() : super('Data tidak ditemukan.', statusCode: 404);
}

class ValidationException extends ApiException {
  final Map<String, dynamic> errors;
  const ValidationException(this.errors) : super('Validasi gagal.', statusCode: 422);

  String get firstError {
    if (errors.isEmpty) return 'Validasi gagal.';
    final first = errors.values.first;
    return first is List ? first.first.toString() : first.toString();
  }
}

class RateLimitException extends ApiException {
  const RateLimitException() : super('Terlalu banyak permintaan. Coba lagi nanti.', statusCode: 429);
}

class ServerException extends ApiException {
  const ServerException() : super('Terjadi kesalahan server. Coba lagi nanti.', statusCode: 500);
}
```

---

## 4. Token Storage (Secure)

```dart
// lib/core/token_storage.dart
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class TokenStorage {
  static const _storage = FlutterSecureStorage();
  static const _tokenKey = 'fundlink_token';
  static const _userKey  = 'fundlink_user';

  static Future<void> saveToken(String token) =>
      _storage.write(key: _tokenKey, value: token);

  static Future<String?> getToken() =>
      _storage.read(key: _tokenKey);

  static Future<void> saveUser(String userJson) =>
      _storage.write(key: _userKey, value: userJson);

  static Future<String?> getUser() =>
      _storage.read(key: _userKey);

  static Future<void> clear() async {
    await _storage.delete(key: _tokenKey);
    await _storage.delete(key: _userKey);
  }

  static Future<bool> hasToken() async {
    final token = await getToken();
    return token != null && token.isNotEmpty;
  }
}
```

---

## 5. HTTP Client

```dart
// lib/core/api_client.dart
import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'config.dart';
import 'exceptions.dart';
import 'token_storage.dart';

class ApiClient {
  static final ApiClient _instance = ApiClient._internal();
  factory ApiClient() => _instance;
  ApiClient._internal();

  Future<Map<String, dynamic>> _headers({bool withAuth = true}) async {
    final headers = <String, String>{
      'Accept':       'application/json',
      'Content-Type': 'application/json',
    };
    if (withAuth) {
      final token = await TokenStorage.getToken();
      if (token != null) headers['Authorization'] = 'Bearer $token';
    }
    return headers;
  }

  // ─── GET ──────────────────────────────────────────────────────────────────

  Future<Map<String, dynamic>> get(String path, {Map<String, String>? params}) async {
    var uri = Uri.parse('${AppConfig.baseUrl}$path');
    if (params != null && params.isNotEmpty) {
      uri = uri.replace(queryParameters: params);
    }
    final response = await http.get(uri, headers: await _headers());
    return _handle(response);
  }

  // ─── POST (JSON) ──────────────────────────────────────────────────────────

  Future<Map<String, dynamic>> post(
    String path,
    Map<String, dynamic> body, {
    bool withAuth = true,
  }) async {
    final response = await http.post(
      Uri.parse('${AppConfig.baseUrl}$path'),
      headers: await _headers(withAuth: withAuth),
      body: jsonEncode(body),
    );
    return _handle(response);
  }

  // ─── PUT (JSON) ───────────────────────────────────────────────────────────

  Future<Map<String, dynamic>> put(String path, Map<String, dynamic> body) async {
    final response = await http.put(
      Uri.parse('${AppConfig.baseUrl}$path'),
      headers: await _headers(),
      body: jsonEncode(body),
    );
    return _handle(response);
  }

  // ─── DELETE ───────────────────────────────────────────────────────────────

  Future<Map<String, dynamic>> delete(String path) async {
    final response = await http.delete(
      Uri.parse('${AppConfig.baseUrl}$path'),
      headers: await _headers(),
    );
    return _handle(response);
  }

  // ─── Multipart (file upload) ──────────────────────────────────────────────

  Future<Map<String, dynamic>> multipart(
    String path,
    Map<String, String> fields, {
    Map<String, File>? files,
    String method = 'POST',
  }) async {
    final token = await TokenStorage.getToken();
    final request = http.MultipartRequest(method, Uri.parse('${AppConfig.baseUrl}$path'));

    request.headers.addAll({
      'Accept':        'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    });

    request.fields.addAll(fields);

    if (files != null) {
      for (final entry in files.entries) {
        request.files.add(await http.MultipartFile.fromPath(entry.key, entry.value.path));
      }
    }

    final streamed = await request.send();
    final response = await http.Response.fromStream(streamed);
    return _handle(response);
  }

  // ─── Response Handler ─────────────────────────────────────────────────────

  Map<String, dynamic> _handle(http.Response response) {
    final body = jsonDecode(response.body) as Map<String, dynamic>;

    switch (response.statusCode) {
      case 200:
      case 201:
        return body;
      case 401:
        TokenStorage.clear(); // hapus token expired
        throw const UnauthorizedException();
      case 403:
        throw const ForbiddenException();
      case 404:
        throw const NotFoundException();
      case 422:
        throw ValidationException(body['errors'] ?? {});
      case 429:
        throw const RateLimitException();
      case 500:
      default:
        throw ServerException();
    }
  }
}
```

---

## 6. Services

```dart
// lib/services/auth_service.dart
import 'dart:convert';
import '../core/api_client.dart';
import '../core/token_storage.dart';
import '../models/user_model.dart';

class AuthService {
  final _client = ApiClient();

  Future<UserModel> login(String email, String password, {String? deviceName}) async {
    final res = await _client.post('/login', {
      'email':       email,
      'password':    password,
      if (deviceName != null) 'device_name': deviceName,
    }, withAuth: false);

    await TokenStorage.saveToken(res['data']['token']);
    final user = UserModel.fromJson(res['data']['user']);
    await TokenStorage.saveUser(jsonEncode(res['data']['user']));
    return user;
  }

  Future<UserModel> register(String name, String email, String password) async {
    final res = await _client.post('/register', {
      'name': name, 'email': email, 'password': password,
    }, withAuth: false);

    await TokenStorage.saveToken(res['data']['token']);
    final user = UserModel.fromJson(res['data']['user']);
    await TokenStorage.saveUser(jsonEncode(res['data']['user']));
    return user;
  }

  Future<void> logout() async {
    try {
      await _client.post('/logout', {});
    } finally {
      await TokenStorage.clear();
    }
  }

  Future<UserModel?> getCurrentUser() async {
    final userJson = await TokenStorage.getUser();
    if (userJson == null) return null;
    return UserModel.fromJson(jsonDecode(userJson));
  }

  Future<bool> isLoggedIn() => TokenStorage.hasToken();
}

// lib/services/transaction_service.dart
import 'dart:io';
import '../core/api_client.dart';
import '../models/transaction_model.dart';
import '../models/pagination_model.dart';

class TransactionService {
  final _client = ApiClient();

  Future<({List<TransactionModel> data, PaginationMeta pagination})> getTransactions({
    int page = 1,
    int perPage = 15,
    String? type,
    String? category,
    String? search,
    String? period,
    int? year,
    int? month,
    String? dateFrom,
    String? dateTo,
    int? unitId,
  }) async {
    final params = <String, String>{
      'page':     page.toString(),
      'per_page': perPage.toString(),
      if (type != null)     'type':      type,
      if (category != null) 'category':  category,
      if (search != null)   'search':    search,
      if (period != null)   'period':    period,
      if (year != null)     'year':      year.toString(),
      if (month != null)    'month':     month.toString(),
      if (dateFrom != null) 'date_from': dateFrom,
      if (dateTo != null)   'date_to':   dateTo,
      if (unitId != null)   'unit_id':   unitId.toString(),
    };

    final res = await _client.get('/transactions', params: params);
    final d   = res['data'] as Map<String, dynamic>;

    return (
      data:       (d['data'] as List).map((e) => TransactionModel.fromJson(e)).toList(),
      pagination: PaginationMeta.fromJson(d['pagination']),
    );
  }

  Future<TransactionModel> getTransaction(int id) async {
    final res = await _client.get('/transactions/$id');
    return TransactionModel.fromJson(res['data']);
  }

  Future<TransactionModel> createTransaction({
    required String type,
    required double amount,
    required String category,
    required String transactionDate,
    String? description,
    int? unitId,
    File? attachment,
  }) async {
    final fields = <String, String>{
      'type':             type,
      'amount':           amount.toString(),
      'category':         category,
      'transaction_date': transactionDate,
      if (description != null) 'description': description,
      if (unitId != null)      'unit_id':     unitId.toString(),
    };

    final res = await _client.multipart(
      '/transactions',
      fields,
      files: attachment != null ? {'attachment': attachment} : null,
    );

    return TransactionModel.fromJson(res['data']);
  }

  Future<TransactionModel> updateTransaction(
    int id, {
    required String type,
    required double amount,
    required String category,
    required String transactionDate,
    required int unitId,
    String? description,
    File? attachment,
  }) async {
    final fields = <String, String>{
      'type':             type,
      'amount':           amount.toString(),
      'category':         category,
      'transaction_date': transactionDate,
      'unit_id':          unitId.toString(),
      if (description != null) 'description': description,
    };

    final res = await _client.multipart(
      '/transactions/$id',
      fields,
      files: attachment != null ? {'attachment': attachment} : null,
    );

    return TransactionModel.fromJson(res['data']);
  }

  Future<void> deleteTransaction(int id) async {
    await _client.delete('/transactions/$id');
  }
}

// lib/services/notification_service.dart
import '../core/api_client.dart';
import '../models/notification_model.dart';
import '../models/pagination_model.dart';

class NotificationService {
  final _client = ApiClient();

  Future<({List<NotificationModel> data, PaginationMeta pagination, int unreadCount})>
      getNotifications({int page = 1}) async {
    final res = await _client.get('/notifications', params: {'page': page.toString()});
    final d   = res['data'] as Map<String, dynamic>;

    return (
      data:        (d['data'] as List).map((e) => NotificationModel.fromJson(e)).toList(),
      pagination:  PaginationMeta.fromJson(d['pagination']),
      unreadCount: d['unread_count'] as int,
    );
  }

  Future<void> markAsRead(int id) => _client.post('/notifications/$id/read', {});
  Future<void> markAllAsRead()    => _client.post('/notifications/read-all', {});
}
```

---

## 7. Contoh Penggunaan di Widget

```dart
// Contoh: Login Screen
class LoginScreen extends StatefulWidget {
  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _authService = AuthService();
  final _emailCtrl   = TextEditingController();
  final _passCtrl    = TextEditingController();
  bool _loading = false;
  String? _error;

  Future<void> _login() async {
    setState(() { _loading = true; _error = null; });
    try {
      final user = await _authService.login(_emailCtrl.text, _passCtrl.text);
      // Navigate to dashboard
      Navigator.pushReplacementNamed(context, '/dashboard');
    } on ValidationException catch (e) {
      setState(() => _error = e.firstError);
    } on ApiException catch (e) {
      setState(() => _error = e.message);
    } finally {
      setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            if (_error != null)
              Container(
                padding: const EdgeInsets.all(12),
                color: Colors.red.shade50,
                child: Text(_error!, style: const TextStyle(color: Colors.red)),
              ),
            TextField(controller: _emailCtrl, decoration: const InputDecoration(labelText: 'Email')),
            TextField(controller: _passCtrl, obscureText: true, decoration: const InputDecoration(labelText: 'Password')),
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: _loading ? null : _login,
              child: _loading ? const CircularProgressIndicator() : const Text('Login'),
            ),
          ],
        ),
      ),
    );
  }
}

// Contoh: Buat Transaksi dengan Foto
Future<void> createTransactionWithPhoto(BuildContext context) async {
  final picker  = ImagePicker();
  final service = TransactionService();

  // Pilih foto
  final picked = await picker.pickImage(source: ImageSource.gallery);
  final file   = picked != null ? File(picked.path) : null;

  try {
    final transaction = await service.createTransaction(
      type:            'pemasukan',
      amount:          500000,
      category:        'Donasi',
      transactionDate: '2025-05-15',
      description:     'Donasi bulan Mei',
      attachment:      file,
    );

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text('Transaksi #${transaction.id} berhasil disimpan')),
    );
  } on ValidationException catch (e) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(e.firstError), backgroundColor: Colors.red),
    );
  }
}
```

---

## 8. App Initialization

```dart
// lib/main.dart
void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  final isLoggedIn = await AuthService().isLoggedIn();

  runApp(MaterialApp(
    initialRoute: isLoggedIn ? '/dashboard' : '/login',
    routes: {
      '/login':     (_) => const LoginScreen(),
      '/dashboard': (_) => const DashboardScreen(),
    },
  ));
}
```

---

## 9. Catatan Penting

### File Upload
- Selalu gunakan `multipart/form-data` untuk endpoint yang ada file
- Jangan kirim base64 — kirim file langsung
- Format yang diterima: `jpg`, `jpeg`, `png`, `webp`, max **2MB**

### Token
- Simpan token di `FlutterSecureStorage` (bukan SharedPreferences biasa)
- Jika response `401`, hapus token dan arahkan ke halaman login
- Sertakan `device_name` saat login untuk manajemen multi-device

### Android Emulator
- Gunakan `http://10.0.2.2:8000/api` untuk akses localhost dari emulator Android
- iOS Simulator: `http://127.0.0.1:8000/api`

### Role Check
- Cek `user.role == 'admin'` sebelum tampilkan fitur admin (unit management, user management)
- API tetap memvalidasi di server — client-side check hanya untuk UX

### Pagination
- Gunakan `pagination.has_more` untuk infinite scroll
- Default `per_page: 15`, maksimum `50`
