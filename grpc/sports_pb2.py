# -*- coding: utf-8 -*-
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: sports.proto
"""Generated protocol buffer code."""
from google.protobuf import descriptor as _descriptor
from google.protobuf import descriptor_pool as _descriptor_pool
from google.protobuf import symbol_database as _symbol_database
from google.protobuf.internal import builder as _builder
# @@protoc_insertion_point(imports)

_sym_db = _symbol_database.Default()




DESCRIPTOR = _descriptor_pool.Default().AddSerializedFile(b'\n\x0csports.proto\"\x0e\n\x0c\x45mptyRequest\"+\n\x11GetSportsResponse\x12\x16\n\x06sports\x18\x01 \x03(\x0b\x32\x06.Sport\"\"\n\x12RemoveSportRequest\x12\x0c\n\x04name\x18\x01 \x01(\t\"7\n\x13RemoveSportResponse\x12\x0f\n\x07success\x18\x01 \x01(\x08\x12\x0f\n\x07message\x18\x02 \x01(\t\"D\n\x05Sport\x12\x0c\n\x04name\x18\x01 \x01(\t\x12\x10\n\x08location\x18\x02 \x01(\t\x12\x0c\n\x04unit\x18\x03 \x01(\t\x12\r\n\x05votes\x18\x04 \x01(\x05\x32y\n\rSportsService\x12.\n\tGetSports\x12\r.EmptyRequest\x1a\x12.GetSportsResponse\x12\x38\n\x0bRemoveSport\x12\x13.RemoveSportRequest\x1a\x14.RemoveSportResponseb\x06proto3')

_globals = globals()
_builder.BuildMessageAndEnumDescriptors(DESCRIPTOR, _globals)
_builder.BuildTopDescriptorsAndMessages(DESCRIPTOR, 'sports_pb2', _globals)
if _descriptor._USE_C_DESCRIPTORS == False:
  DESCRIPTOR._options = None
  _globals['_EMPTYREQUEST']._serialized_start=16
  _globals['_EMPTYREQUEST']._serialized_end=30
  _globals['_GETSPORTSRESPONSE']._serialized_start=32
  _globals['_GETSPORTSRESPONSE']._serialized_end=75
  _globals['_REMOVESPORTREQUEST']._serialized_start=77
  _globals['_REMOVESPORTREQUEST']._serialized_end=111
  _globals['_REMOVESPORTRESPONSE']._serialized_start=113
  _globals['_REMOVESPORTRESPONSE']._serialized_end=168
  _globals['_SPORT']._serialized_start=170
  _globals['_SPORT']._serialized_end=238
  _globals['_SPORTSSERVICE']._serialized_start=240
  _globals['_SPORTSSERVICE']._serialized_end=361
# @@protoc_insertion_point(module_scope)
